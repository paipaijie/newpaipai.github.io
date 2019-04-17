<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/23
 * Time: 17:08
 */
namespace App\Modules\Mapi\Controllers;

class PaiController extends \App\Modules\Mapi\Foundation\Controller
{

    //拍拍详情
    public function actionDetails(){

        $group_buy_id=$_REQUEST['ppj_id'];
        $user_id=$_REQUEST['uid'];
        if(!$group_buy_id){
            $pai_data=array(
                'code'=> 10003,
                'data'=> '无效数据'
            );
            $this->resp(array('data' =>$pai_data));
        }

        $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('paipai_list') .'WHERE ppj_id =' . $group_buy_id ;

        $group_buy = $GLOBALS['db']->getRow($sql);

        if (!$group_buy) {
            $pai_data=array(
                'code'=> 10002,
                'data'=> '暂无数据'
            );
            $this->resp(array('data' =>$pai_data));
        }

        //获取当前价格
        $pay_margin_amount_sql='SELECT count(ls_pay_ok) as amount FROM '.$GLOBALS['ecs']->table('paipai_seller_pay_margin').' WHERE ppj_id='.$group_buy['ppj_id'].' AND ppj_no='.$group_buy['ppj_no'].' AND ls_pay_ok=1';
        $pay_margin_amount=$GLOBALS['db']->getOne($pay_margin_amount_sql);
        $ext_info = unserialize($group_buy['ext_info']);
        $group_buy = array_merge($group_buy, $ext_info);
        $price_ladder = $group_buy['price_ladder'];
        if (!is_array($price_ladder) || empty($price_ladder)) {
            $price_ladder = array(
                array('amount' => 0, 'price' => 0)
            );
        }
        else {
            foreach ($price_ladder as $key => $amount_price) {
                $price_ladder[$key]['formated_price'] = price_format($amount_price['price'], false);
            }
        }
        $group_buy['price_ladder'] = $price_ladder;
        $stat = group_buy_stat($group_buy_id, $group_buy['deposit']);
        $group_buy = array_merge($group_buy, $stat);
        $cur_price = $price_ladder[0]['price'];
        $cur_amount = $stat['valid_goods'] + $pay_margin_amount;
        foreach ($price_ladder as $amount_price) {
            if ($amount_price['amount'] <= $cur_amount) {
                $cur_price = $amount_price['price'];
            }
            else if($pay_margin_amount == 0 ) {
                $cur_price=0;break;
            }
        }

        $pai_data['ppj_id']=$group_buy['ppj_id'];  //id
        $pai_data['goods_name']=$group_buy['goods_name'];   //商品名
        $pai_data['goods_id']=$group_buy['goods_id'];  //商品id
        $pai_data['ppj_no']=$group_buy['ppj_no'];  //期数
        $pai_data['ppj_margin_fee']=$group_buy['ppj_margin_fee'];   //保证金
        $pai_data['cur_amount']= $pay_margin_amount;   //当前参与人数
        $pai_data['cur_price']=price_format($cur_price,false);    //当前价
        $pai_data['restrict_amount'] =$group_buy['goods_count'] ;   //限购数
        $pai_data['gift_integral']=$group_buy['gift_integral']?$group_buy['gift_integral']:'0';     //积分
        //活动状态判定
        //当前活动已支付销量
        $order_ok_sql='SELECT count(order_id) as amount FROM '.$GLOBALS['ecs']->table('order_info').' WHERE ppj_id='.$group_buy['ppj_id'].' AND ppj_no='.$group_buy['ppj_no'].' AND pay_status=2 ';
        $order_ok_count=$GLOBALS['db']->getOne($order_ok_sql);
        $now_time=time()+8*3600;
        $ppj_start_time=$group_buy['start_time'];
        $ppj_end_time=$group_buy['end_time'];
        $now_h=date('H',$now_time);
        $pai_data['now_hours']=$now_h;
        $sale_max_time=$group_buy['ppj_sale_time']+2;
        if($now_time >= $ppj_start_time && $now_time <= $ppj_end_time){
               if($now_h >= $group_buy['ppj_sale_time'] &&  $now_h < $sale_max_time){
                   if($order_ok_count < $group_buy['goods_count']){
                        $status='1';
                   }else{
                       $status='2';
                   }
               }else{
                   $status='2';
               }
        }else{
            $status='2';
        }
        $pai_data['active_status']=$status;   // 活动状态
        $pai_data['sell_count']=$pay_margin_amount;    //销量
        //倒计时
        $pai_data['count_down']='2';

        //滚动图
        $scroll_sql = 'SELECT img_url,thumb_url FROM '. $GLOBALS['ecs']->table('goods_gallery') .' WHERE goods_id = ' .$group_buy['goods_id'].' ORDER BY img_id ASC';
        $scroll_img = $GLOBALS['db']->getAll($scroll_sql);
        foreach ($scroll_img as $key => $val) {
            $scroll_img_data[$key]['img_url'] = get_image_path($val['img_url']);
        }
        $pai_data['scroll_data']['count']=count($scroll_img_data);   // 活动状态
        $pai_data['scroll_data']['scroll_img']=$scroll_img_data;
        //商品属性
        $attr_sql = 'SELECT goods_attr_id,attr_id,attr_value FROM '. $GLOBALS['ecs']->table('goods_attr') .' WHERE goods_id = ' .$group_buy['goods_id'];
        $goods_attr = $GLOBALS['db']->getAll($attr_sql);

        $pai_data['goods_format']['count']=count($goods_attr);
        $pai_data['goods_format']['format']=$goods_attr?$goods_attr:'0';
        //详情
        $link_sql = 'SELECT ld.goods_desc FROM {pre}link_desc_goodsid AS dg, {pre}link_goods_desc AS ld WHERE dg.goods_id = ' . $group_buy['goods_id'] . '  AND dg.d_id = ld.id AND ld.review_status > 2';
        $link_desc = $GLOBALS['db']->getOne($link_sql);

        $goods = goods_info($group_buy['goods_id'], 0, 0, 0);

        if (!empty($goods['desc_mobile'])) {
            if (C('shop.open_oss') == 1) {

                $bucket_info = get_bucket_info();
                $bucket_info['endpoint'] = empty($bucket_info['endpoint']) ? $bucket_info['outside_site'] : $bucket_info['endpoint'];
                $desc_preg = get_goods_desc_images_preg($bucket_info['endpoint'], $goods['desc_mobile'], 'desc_mobile');
                $goods_desc = preg_replace('/<div[^>]*(tools)[^>]*>(.*?)<\\/div>(.*?)<\\/div>/is', '', $desc_preg['desc_mobile']);
            }
            else {
                $goods_desc = preg_replace('/<div[^>]*(tools)[^>]*>(.*?)<\\/div>(.*?)<\\/div>/is', '', $goods['desc_mobile']);
            }
        }
        if (empty($goods['desc_mobile']) && !empty($goods['goods_desc'])) {
            if (C('shop.open_oss') == 1) {
                $bucket_info = get_bucket_info();
                $bucket_info['endpoint'] = empty($bucket_info['endpoint']) ? $bucket_info['outside_site'] : $bucket_info['endpoint'];
                $goods_desc = str_replace(array('src="/images/upload', 'src="images/upload'), 'src="' . $bucket_info['endpoint'] . 'images/upload', $goods['goods_desc']);
            }
            else {
                $goods_desc = str_replace(array('src="/images/upload', 'src="images/upload'), 'src="' . __STATIC__ . '/images/upload', $goods['goods_desc']);
            }
        }
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$goods_desc,$match);
        $pai_data['details_img']=$match[1];      //商品详情
        $pai_data['stock_num']=$goods['goods_number'];  //库存数
        $pai_data['market_price']=$goods['market_price']; //商城价
        $pai_data['shop_price']=$goods['shop_price']; //市场价
        $pai_data['thumb_img']=get_image_path($goods['goods_thumb']);  //缩略图
        $pai_data['shop_name']=$goods['shop_name'];  //商户名
        $pai_data['kf_qq']=$goods['kf_qq'];  //客服QQ

        //运费
        $pai_data['shipping_fee']=$goods['shipping_fee']?$goods['shipping_fee']:'免运费';


        $pai_data['goods_collect']='no';
        if($user_id){
            //收藏   //yes为收藏  no位为收藏
            $collect_sql = 'SELECT user_id,goods_id,is_attention FROM '. $GLOBALS['ecs']->table('collect_goods') .' WHERE goods_id = ' .$group_buy['goods_id'].' AND user_id='.$user_id;
            $goods_collect_data = $GLOBALS['db']->getRow($collect_sql);
            if($goods_collect_data){
                $pai_data['goods_collect']='yes';
            }

            //活动是否参加
            $margin_sql = 'SELECT spm_id,user_id,order_id  FROM '. $GLOBALS['ecs']->table('paipai_seller_pay_margin') .' WHERE ppj_id = ' .$group_buy['ppj_id'].' AND user_id='.$user_id.' AND ls_pay_ok=1 ORDER BY spm_id DESC';
            $margin_data = $GLOBALS['db']->getRow($margin_sql);
            if($margin_data){
                $m_order_sql='SELECT order_id,user_id  FROM '. $GLOBALS['ecs']->table('order_info') .' WHERE order_id= ' .$margin_data['order_id'].' AND pay_status=10';
                $m_order_data = $GLOBALS['db']->getRow($m_order_sql);
                if($margin_data && $m_order_data){
                    $u_bid_sql = 'SELECT bid_price,bid_time,is_status FROM '. $GLOBALS['ecs']->table('paipai_goods_bid_user') .' WHERE spm_id='.$margin_data['spm_id'].' AND ls_pay_ok=1';
                    $u_bid_data = $GLOBALS['db']->getRow($u_bid_sql);
                    if($u_bid_data){
                        $pai_data['margin_bid']=array(
                            'margin_status'=>'1',
                            'bid_price'=>$u_bid_data['bid_price']
                        );
                    }
                }
            }else{
                $pai_data['margin_bid']=array(
                    'margin_status'=>'0',
                    'bid_price'=>'0.00'
                );
            }
        }else{
            $pai_data['margin_bid']=array(
                'margin_status'=>'0',
                'bid_price'=>'0.00'
            );
        }


        $this->resp(array('data' =>$pai_data));
    }

    //提交抢购
    public function actionPaicart(){
        $ppj_id=$_REQUEST['ppj_id'];
        $user_id=$_REQUEST['uid'];
        $sellers_fee=$_REQUEST['sellers_fee'];
        $norm=$_REQUEST['norm'];
        $norm2=$_REQUEST['norm2'];
        if(!$ppj_id || !$user_id || !$sellers_fee){
            $pai_data=array(
                'code'=> 10003,
                'data'=> '无效数据'
            );
            $this->resp(array('data' =>$pai_data));
        }
        $pl_sql='SELECT ppj_id,ppj_no,goods_id,gift_integral FROM '. $GLOBALS['ecs']->table('paipai_list').' WHERE ppj_id='.$ppj_id;
        $pl_data = $GLOBALS['db']->getRow($pl_sql);
        if($pl_data){
            $goods_sql='SELECT goods_id,goods_name,goods_sn,market_price,shop_price FROM '. $GLOBALS['ecs']->table('goods').' WHERE goods_id='.$pl_data['goods_id'];
            $goods_data = $GLOBALS['db']->getRow($goods_sql);
        }else{
            $pai_data=array(
                'code'=> 10003,
                'data'=> '无效的活动id'
            );
            $this->resp(array('data' =>$pai_data));
        }
        $goods_attr='';
        $goods_attr_id='';
        if($goods_data){
            if($norm){
                $goods_attr_sql1='SELECT goods_attr_id,goods_id,attr_id,attr_value FROM '. $GLOBALS['ecs']->table('goods_attr').' WHERE goods_attr_id='.$norm;
                $goods_attr_data1 = $GLOBALS['db']->getRow($goods_attr_sql1);
                $goods_attr=$goods_attr_data1['attr_value'];
                $goods_attr_id=$goods_attr_data1['goods_attr_id'];
            }
            if($norm2){
                $goods_attr_sql2='SELECT goods_attr_id,goods_id,attr_id,attr_value FROM '. $GLOBALS['ecs']->table('goods_attr').' WHERE goods_attr_id='.$norm2;
                $goods_attr_data2 = $GLOBALS['db']->getRow($goods_attr_sql2);
                $goods_attr=$goods_attr_data2['attr_value'];
                $goods_attr_id=$goods_attr_data2['goods_attr_id'];
            }
            if($goods_attr_data1 && $goods_attr_data2){
                $goods_attr=$goods_attr_data1['attr_value'].'.'.$goods_attr_data2['attr_value'];
                $goods_attr_id=$goods_attr_data1['goods_attr_id'].'.'.$goods_attr_data2['goods_attr_id'];
            }
            $pai_cart='SELECT rec_id FROM '. $GLOBALS['ecs']->table('cart').' WHERE user_id='.$user_id.' AND ppj_id>0 ';
            $pai_cart_data = $GLOBALS['db']->getRow($pai_cart);
            if($pai_cart_data){
                $upd_cart_data=array(
                    'goods_id'=>$goods_data['goods_id'],
                    'goods_sn'=>$goods_data['goods_sn'],
                    'goods_name'=>$goods_data['goods_name'],
                    'market_price'=>$goods_data['market_price'],
                    'goods_price'=>$goods_data['shop_price'],
                    'goods_attr'=>$goods_attr,
                    'is_gift'=>$pl_data['gift_integral'],
                    'goods_attr_id'=>$goods_attr_id,
                    'add_time'=>time()+8*3600,
                    'sellers_fee'=>$sellers_fee,
                    'ppj_id'=>$pl_data['ppj_id'],
                    'ppj_no'=>$pl_data['ppj_no']
                );
                $res=$this->db->autoExecute($GLOBALS['ecs']->table('cart'), $upd_cart_data, 'UPDATE',array('rec_id'=>$pai_cart_data['rec_id']));
            }else{
                $ins_cart_data=array(
                    'user_id'=>$user_id,
                    'goods_id'=>$goods_data['goods_id'],
                    'goods_sn'=>$goods_data['goods_sn'],
                    'goods_name'=>$goods_data['goods_name'],
                    'market_price'=>$goods_data['market_price'],
                    'goods_price'=>$goods_data['shop_price'],
                    'goods_number'=>'1',
                    'goods_attr'=>$goods_attr,
                    'is_real'=>'1',
                    'rec_type'=>'1',
                    'is_gift'=>$pl_data['gift_integral'],
                    'goods_attr_id'=>$goods_attr_id,
                    'warehouse_id'=>'2',
                    'area_id'=>'24',
                    'area_city'=>'0',
                    'add_time'=>time()+8*3600,
                    'is_checked'=>'1',
                    'sellers_fee'=>$sellers_fee,
                    'ppj_id'=>$pl_data['ppj_id'],
                    'ppj_no'=>$pl_data['ppj_no']
                );
                $res=$this->db->autoExecute($GLOBALS['ecs']->table('cart'), $ins_cart_data, 'INSERT');
            }
        }else{
            $pai_data=array(
                'code'=> 10003,
                'data'=> '无效的商品id'
            );
            $this->resp(array('data' =>$pai_data));
        }

        $this->resp(array('add_res' =>$res));


    }
}

?>