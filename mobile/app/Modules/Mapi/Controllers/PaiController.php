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
                        $pai_data['user_price']=array(
                            'margin_status'=>'1',
                            'bid_price'=>$u_bid_data['bid_price']
                        );
                    }
                }
            }else{
                $pai_data['user_price']=array(
                    'margin_status'=>'0',
                    'bid_price'=>'0.00'
                );
            }
        }else{
            $pai_data['user_price']=array(
                'margin_status'=>'0',
                'bid_price'=>'0.00'
            );
        }
        if($pai_data){
            $pai_res=array(
                'code'=> 10001,
                'data'=>$pai_data
            );
        }else{
            $pai_res=array(
                'code'=> 10002,
                'data'=>'暂无数据'
            );
        }

        $this->resp(array('details' =>$pai_res));

    }

    //提交抢购
    public function actionPaicart(){
        $ppj_id=$_REQUEST['ppj_id'];
        $user_id=$_REQUEST['uid'];
        $sellers_fee=$_REQUEST['sellers_fee'];
        $norm=$_REQUEST['norm'];
        if(!$ppj_id || !$user_id || !$sellers_fee){
            $pai_data=array(
                'code'=> 10003,
                'data'=> '无效数据'
            );
            $this->resp(array('data' =>$pai_data));
        }
        $pl_sql='SELECT ppj_id,ppj_no,goods_id,ppj_margin_fee,gift_integral FROM '. $GLOBALS['ecs']->table('paipai_list').' WHERE ppj_id='.$ppj_id;
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
                $norm_arr=explode("_", $norm);
                for($i=0;$i<count($norm_arr);$i++){
                    $goods_attr_sql='SELECT goods_attr_id,goods_id,attr_id,attr_value FROM '. $GLOBALS['ecs']->table('goods_attr').' WHERE goods_attr_id='.$norm_arr[$i];
                    $goods_attr_data[$i] = $GLOBALS['db']->getRow($goods_attr_sql);
                    $goods_attr.=$goods_attr_data[$i]['attr_value'].',';
                    $goods_attr_id.=$goods_attr_data[$i]['goods_attr_id'].',';
                }
                $goods_attr = substr( $goods_attr,0, strlen($goods_attr)-1 );
                $goods_attr_id = substr( $goods_attr_id,0, strlen($goods_attr_id)-1 );
            }

            $pai_cart='SELECT rec_id FROM '. $GLOBALS['ecs']->table('cart').' WHERE user_id='.$user_id.' AND ppj_id>0 ';
            $pai_cart_data = $GLOBALS['db']->getRow($pai_cart);
            if($pai_cart_data){
                $upd_cart_data=array(
                    'goods_id'=>$goods_data['goods_id'],
                    'goods_sn'=>$goods_data['goods_sn'],
                    'product_id'=>'0',
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
                $where='rec_id='.$pai_cart_data['rec_id'];
                $res=$this->db->autoExecute($GLOBALS['ecs']->table('cart'), $upd_cart_data, 'UPDATE',$where);
            }else{
                $ins_cart_data=array(
                    'user_id'=>$user_id,
                    'goods_id'=>$goods_data['goods_id'],
                    'goods_sn'=>$goods_data['goods_sn'],
                    'product_id'=>'0',
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

            //保证金记录添加
            if($res){
                $margin_sql='SELECT m.spm_id,b.bid_id FROM '. $GLOBALS['ecs']->table('paipai_seller_pay_margin').' AS m LEFT JOIN'.$GLOBALS['ecs']->table('paipai_goods_bid_user').' AS b ON m.spm_id=b.spm_id WHERE m.user_id='.$user_id.' AND m.ppj_id='.$ppj_id;
                $margin_data = $GLOBALS['db']->getRow($margin_sql);
                if($margin_data){
                    $add_res=array(
                        'code'=> 10001,
                        'data'=>'1'
                    );
                }else{
                    $ins_margin_data=array(
                        'user_id'=>$user_id,
                        'ppj_id'=>$pl_data['ppj_id'],
                        'ppj_no'=>$pl_data['ppj_no'],
                        'pay_fee'=>$pl_data['ppj_margin_fee'],
                        'ls_pay_ok'=>'0',
                        'ls_refund'=>'0',
                        'createtime'=>time()+8*3600
                    );
                    $m_ins_res=$this->db->autoExecute($GLOBALS['ecs']->table('paipai_seller_pay_margin'), $ins_margin_data, 'INSERT');
                    if($m_ins_res){
                        $sel_margin_sql='SELECT spm_id FROM '. $GLOBALS['ecs']->table('paipai_seller_pay_margin').' WHERE user_id='.$user_id.' AND ppj_id='.$ppj_id.' ORDER BY spm_id DESC LIMIT 1';
                        $sel_margin_data = $GLOBALS['db']->getRow($sel_margin_sql);
                        $ins_bid_data=array(
                            'user_id'=>$user_id,
                            'ppj_id'=>$pl_data['ppj_id'],
                            'ppj_no'=>$pl_data['ppj_no'],
                            'bid_price'=>$sellers_fee,
                            'bid_time'=>time()+8*3600,
                            'is_status'=>'0',
                            'createtime'=>time()+8*3600,
                            'spm_id'=>$sel_margin_data['spm_id'],
                        );
                        $bid_ins_res=$this->db->autoExecute($GLOBALS['ecs']->table('paipai_goods_bid_user'), $ins_bid_data, 'INSERT');
                        if($bid_ins_res){
                            $add_res=array(
                                'code'=> 10001,
                                'data'=> $bid_ins_res
                            );
                        }else{
                            $add_res=array(
                                'code'=> 10004,
                                'data'=> '数据(出价记录)处理失败'
                            );
                        }
                    }else{
                        $add_res=array(
                            'code'=> 10004,
                            'data'=>'数据(保证金)处理失败'
                        );
                    }
                }
                $this->resp(array('data' =>$add_res));
            }else{
                $add_res=array(
                    'code'=> 10004,
                    'data'=>'数据(购物车)处理失败'
                );
                $this->resp(array('data' =>$add_res));
            }
        }else{
            $pai_data=array(
                'code'=> 10003,
                'data'=> '无效的商品id'
            );
            $this->resp(array('data' =>$pai_data));
        }


    }

    //提交结算页
    public function actionPaidetails(){
        $user_id=$_REQUEST['uid'];
        if(!$user_id){
            $pai_data=array(
                'code'=> 10003,
                'data'=> '无效数据'
            );
            $this->resp(array('data' =>$pai_data));
        }
        $u_pai_cart_sql='SELECT goods_id,sellers_fee,goods_number,goods_attr,goods_attr_id,ppj_id,ppj_no FROM '. $GLOBALS['ecs']->table('cart').' WHERE user_id='.$user_id.' AND ppj_id>0 ';
        $u_pai_cart_data = $GLOBALS['db']->getRow($u_pai_cart_sql);
        if(!$u_pai_cart_data){
            $pai_data=array(
                'code'=> 10002,
                'data'=> '暂无数据'
            );
            $this->resp(array('data' =>$pai_data));
        }
        //收货地址
        $user_sql = 'SELECT address_id FROM ' . $GLOBALS['ecs']->table('users') . '  WHERE user_id='.$user_id;
        $user_data = $GLOBALS['db']->getRow($user_sql);
        $default_address = $user_data['address_id'];

        $user_address = get_order_user_address_list($user_id);
        foreach($user_address as $akey=>$aval) {
            $consignee['province_name'] = get_goods_region_name($aval['province']);
            $consignee['city_name'] = get_goods_region_name($aval['city']);
            $consignee['district_name'] = get_goods_region_name($aval['district']);
            $street = get_region_name($aval['street']);
            $consignee['street_name'] = $street['region_name'];
            $user_address[$akey]['region'] = $consignee['province_name'] . ' ' . $consignee['city_name']. ' ' .$consignee['district_name'] . ' ' . $consignee['street_name'];
            $user_address[$akey]['default_address']=$aval['address_id']==$default_address?'1':'0';
        }
        if($user_address){
            $user_address_data=array(
                'code'=> 10001,
                'data'=> $user_address
            );
        }else{
            $user_address_data=array(
                'code'=> 10002,
                'data'=> '暂无数据'
            );
        }

        //活动详情
        $pl_sql='SELECT ppj_id,ppj_no,ppj_name,ppj_margin_fee,ppj_startpay_time,ppj_endpay_time,ppj_staus FROM '. $GLOBALS['ecs']->table('paipai_list').' WHERE ppj_id='.$u_pai_cart_data['ppj_id'];
        $pl_data = $GLOBALS['db']->getRow($pl_sql);
        //商品详情
        $g_sql='SELECT goods_name,goods_thumb FROM '. $GLOBALS['ecs']->table('goods').' WHERE goods_id='.$u_pai_cart_data['goods_id'];
        $g_data = $GLOBALS['db']->getRow($g_sql);
        //规格
        if($u_pai_cart_data['goods_attr_id']){
            $attr_id_row=explode(",",$u_pai_cart_data['goods_attr_id']);
        }
        $pai_detail['ppj_id']=$pl_data['ppj_id'];
        $pai_detail['ppj_no']=$pl_data['ppj_no'];
        $pai_detail['goods_thumb']='http://www.paipaistreet.com'.get_image_path($g_data['goods_thumb']);
        $pai_detail['goods_name']=$g_data['goods_name'];
        $pai_detail['margin_fee']=$pl_data['ppj_margin_fee'];
        $pai_detail['sellers_fee']=$u_pai_cart_data['sellers_fee'];
        $pai_detail['goods_attr']=$u_pai_cart_data['goods_attr'];
        $pai_detail['goods_attr_id']=$u_pai_cart_data['goods_attr_id'];
        $pai_detail['goods_number']=$u_pai_cart_data['goods_number'];

        if($pai_detail){
            $pai_detail_data=array(
                'code'=> 10001,
                'data'=> $pai_detail
            );
        }else{
            $pai_detail_data=array(
                'code'=> 10002,
                'data'=> '暂无数据'
            );
        }

        //支付方式
        if($pai_detail['margin_fee']>0){
            $pay_sql='SELECT pay_id,pay_code,pay_name,pay_config FROM '. $GLOBALS['ecs']->table('payment').' WHERE enabled=1 AND pay_id!=16';
            $payment_data = $GLOBALS['db']->getAll($pay_sql);
            foreach($payment_data as $pykey=>$pyval){
                $payment_data[$pykey]['pay_config']=unserialize($pyval['pay_config']);
            }
        }else{
            $payment_data['pay_id']='15';
            $payment_data['pay_code']='onlinepay';
            $payment_data['pay_name']='免保证金';
 //           $payment_data['pay_config']='';
        }

        $this->resp(array('address' =>$user_address_data,'pai_details'=>$pai_detail_data,'pay_way'=>$payment_data));
    }

    //提交拍拍活动保证金支付
    public function actionPaibuy(){

        $user_id=$_REQUEST['uid'];
        $address_id=$_REQUEST['address_id'];
        $ppj_id=$_REQUEST['ppj_id'];
        $pay_id=$_REQUEST['pay_id'];
        $shipping_id=$_REQUEST['shipping_id'];
        if(!$shipping_id){
            $shipping_id='2';
        }
        if(!$user_id && !$address_id && !$ppj_id && !$pay_id){
            $pai_data=array(
                'code'=> 10003,
                'data'=> '无效数据'
            );
            $this->resp(array('data' =>$pai_data));
        }
        //购物车信息
        $c_sql='SELECT rec_id,goods_id,goods_sn,goods_name,market_price,goods_price,goods_number,goods_attr,goods_attr_id,sellers_fee,ppj_id,ppj_no FROM '. $GLOBALS['ecs']->table('cart').' WHERE user_id='.$user_id.' AND ppj_id='.$ppj_id;
        $cart_data = $GLOBALS['db']->getRow($c_sql);
        //订单号
        $order['order_sn'] = get_order_sn();
        $order['user_id'] = $user_id;
        //收件人、地址
        $ud_sql='SELECT consignee,email,country,province,city,district,street,address,mobile FROM '. $GLOBALS['ecs']->table('user_address').' WHERE address_id='.$address_id;
        $ud_data = $GLOBALS['db']->getRow($ud_sql);
        $order['consignee'] =$ud_data['consignee'];
        $order['email'] =$ud_data['email'];
        $order['country'] =$ud_data['country'];
        $order['province'] =$ud_data['province'];
        $order['city'] =$ud_data['city'];
        $order['district'] =$ud_data['district'];
        $order['street'] =$ud_data['street'];
        $order['address'] =$ud_data['address'];
        $order['mobile'] =$ud_data['mobile'];
        //快递
        $shiping_sql='SELECT shipping_id,shipping_code,shipping_name FROM '. $GLOBALS['ecs']->table('shipping').' WHERE shipping_id='.$shipping_id;
        $shipping_data = $GLOBALS['db']->getRow($shiping_sql);
        $order['shipping_id'] =$shipping_data['shipping_id'];
        $order['shipping_name'] =$shipping_data['shipping_name'];
        $order['shipping_code'] =$shipping_data['shipping_code'];
        //支付方式
        $pay_sql='SELECT pay_id,pay_code,pay_name,pay_fee,pay_config FROM '. $GLOBALS['ecs']->table('payment').' WHERE pay_id='.$pay_id;
        $pay_data = $GLOBALS['db']->getRow($pay_sql);
        $order['pay_id'] =$pay_data['pay_id'];
        $order['pay_name'] =$pay_data['pay_name'];
        $order['pay_config'] =unserialize($pay_data['pay_config']);
        //活动详情
        $pl_sql='SELECT ppj_no,ppj_name,ppj_margin_fee,ppj_startpay_time,ppj_endpay_time,ppj_staus,goods_id FROM '. $GLOBALS['ecs']->table('paipai_list').' WHERE ppj_id='.$ppj_id;
        $pl_data = $GLOBALS['db']->getRow($pl_sql);
        //商品详情
        $g_sql='SELECT goods_id,goods_sn,goods_name,market_price,shop_price,shipping_fee,is_real FROM '. $GLOBALS['ecs']->table('goods').' WHERE goods_id='.$pl_data['goods_id'];
        $goods_data = $GLOBALS['db']->getRow($g_sql);
        //订单金额
        if($pl_data['ppj_margin_fee']==0){
            $order['pay_status'] = '10';
            $order['order_status']='1';
        }else{
            $order['pay_status'] = '11';
        }
        $margin_sql='SELECT pay_fee FROM '. $GLOBALS['ecs']->table('paipai_seller_pay_margin').' WHERE user_id='.$user_id.' AND ppj_id='.$ppj_id.' AND ls_pay_ok=0';
        $margin_data = $GLOBALS['db']->getRow($margin_sql);
        $order['goods_amount'] =$margin_data['pay_fee'];
        $order['money_paid'] =$margin_data['pay_fee'];
        $order['order_amount'] =$margin_data['pay_fee'];

        $order['referer'] ='touch';
        $order['add_time'] =time()+8*3600;
        $order['extension_code'] ='paipai_buy';
        $order['extension_id'] =$goods_data['goods_id'];
        $order['ppj_id'] =$ppj_id;
        $order['ppj_no'] =$pl_data['ppj_no'];
        $ins_oi_res=$this->db->autoExecute($GLOBALS['ecs']->table('order_info'), $order, 'INSERT');
        if($ins_oi_res){
            $sel_oi_sql='SELECT order_id,order_sn FROM '. $GLOBALS['ecs']->table('order_info').' WHERE order_sn='.$order['order_sn'];
            $sel_oi_data = $GLOBALS['db']->getRow($sel_oi_sql);
            //订单商品表数据
            $order_og['order_id']=$sel_oi_data['order_id'];
            $order_og['user_id']=$user_id;
            $order_og['goods_id']=$goods_data['goods_id'];
            $order_og['goods_name']=$goods_data['goods_name'];
            $order_og['goods_sn']=$goods_data['goods_sn'];
            $order_og['goods_number']=$cart_data['goods_number'];
            $order_og['market_price']=$goods_data['market_price'];
            $order_og['goods_price']=$goods_data['shop_price'];
            $order_og['goods_attr']=$cart_data['goods_attr'];
            $order_og['send_number']=$cart_data['goods_number'];
            $order_og['is_real']=$goods_data['is_real'];
            $order_og['goods_attr_id']=$cart_data['goods_attr_id'];
            $order_og['warehouse_id']='2';
            $order_og['area_id']='24';
            $order_og['sellers_fee']=$cart_data['sellers_fee'];
            $order_og['ppj_no']=$cart_data['ppj_no'];
            $ins_og_res=$this->db->autoExecute($GLOBALS['ecs']->table('order_goods'), $order_og, 'INSERT');
            if($ins_og_res){
                //更新保证金订单号
                $sel_spm_sql='SELECT spm_id FROM '. $GLOBALS['ecs']->table('paipai_seller_pay_margin').' WHERE user_id='.$user_id.' AND ppj_id='.$ppj_id.' AND ls_pay_ok=0 ';
                $spm_data = $GLOBALS['db']->getRow($sel_spm_sql);
                if($pl_data['ppj_margin_fee']==0){
                    $upd_margin_data=array('order_id'=>$sel_oi_data['order_id'],'order_sn'=>$sel_oi_data['order_sn'],'ls_pay_ok'=>'1','paytime'=>time()+8*3600);
                }else{
                    $upd_margin_data=array('order_id'=>$sel_oi_data['order_id'],'order_sn'=>$sel_oi_data['order_sn']);
                }
                $up_margin_where=' spm_id='.$spm_data['spm_id'];
                $up_margin_res=$this->db->autoExecute($GLOBALS['ecs']->table('paipai_seller_pay_margin'), $upd_margin_data, 'UPDATE', $up_margin_where);
                if($up_margin_res){
                    if($pl_data['ppj_margin_fee']==0){
                        $upd_bid_data=array('is_status'=>'2');
                        $up_bid_res=$this->db->autoExecute($GLOBALS['ecs']->table('paipai_goods_bid_user'), $upd_bid_data, 'UPDATE', $up_margin_where);
                    }
                    $paibuy_data=array(
                        'code'=> 10001,
                        'data'=> $up_margin_res
                    );
                    $pay_data=array(
                        'code'=> 10001,
                        'data'=> $order['pay_config']
                    );
                }else{
                    $paibuy_data=array(
                        'code'=> 10004,
                        'data'=> '数据(更新保证金订单号)处理失败'
                    );
                }
            }else{
                $paibuy_data=array(
                    'code'=> 10004,
                    'data'=> '数据(订单商品)处理失败'
                );
            }
        }else{
            $paibuy_data=array(
                'code'=> 10004,
                'data'=> '数据(订单信息)处理失败'
            );
        }

        $this->resp(array('data' =>$paibuy_data,'pay_data'=>$pay_data));
    }


}
?>