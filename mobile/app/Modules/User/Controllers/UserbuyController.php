<?php
//zend by 多点乐  禁止倒卖 一经发现停止任何服务
namespace App\Modules\User\Controllers;

class UserbuyController extends \App\Modules\Base\Controllers\FrontendController
{
    public $user_id;

    public function __construct()
    {
        parent::__construct();
        L(require LANG_PATH . C('shop.lang') . '/user.php');
        L(require LANG_PATH . C('shop.lang') . '/flow.php');
        $this->assign('lang', array_change_key_case(L()));
        $files = array('order', 'clips', 'transaction');
        $this->load_helper($files);
        $this->check_login();

        $this->user_id = $_SESSION['user_id'];
        $this->assign('user_id', $this->user_id);

        if (!empty($_SESSION['user_id'])) {
            $this->sess_id = ' user_id = \'' . $_SESSION['user_id'] . '\' ';
            $this->a_sess = ' a.user_id = \'' . $_SESSION['user_id'] . '\' ';
            $this->b_sess = ' b.user_id = \'' . $_SESSION['user_id'] . '\' ';
            $this->c_sess = ' c.user_id = \'' . $_SESSION['user_id'] . '\' ';
            $this->sess_ip = '';
        }
        else {
            $this->sess_id = ' session_id = \'' . real_cart_mac_ip() . '\' ';
            $this->a_sess = ' a.session_id = \'' . real_cart_mac_ip() . '\' ';
            $this->b_sess = ' b.session_id = \'' . real_cart_mac_ip() . '\' ';
            $this->c_sess = ' c.session_id = \'' . real_cart_mac_ip() . '\' ';
            $this->sess_ip = real_cart_mac_ip();
        }

        $area_info = get_area_info($this->province_id);
        $this->area_id = $area_info['region_id'];
        $where = 'regionId = \'' . $this->province_id . '\'';
        $date = array('parent_id');
        $this->region_id = get_table_date('region_warehouse', $where, $date, 2);
        if (isset($_COOKIE['region_id']) && !empty($_COOKIE['region_id'])) {
            $this->region_id = $_COOKIE['region_id'];
        }

        $other = array('province_id' => $this->province_id, 'city_id' => $this->city_id);
        $warehouse_area_info = get_warehouse_area_info($other);
        $this->area_city = $warehouse_area_info['city_id'];
    }



    public function actionUserbuy()
    {
        $order_id=$_GET['id'];

        $order_sql="SELECT * FROM ".$GLOBALS['ecs']->table('order_info')." WHERE order_id=".$order_id;
        $order=$GLOBALS['db']->getRow($order_sql); 

        if(empty($order)){
            ecs_header('Location: ' . url('user/userbuy/buyerror'));
            exit();
        }       
        $pl_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_list'). " WHERE ppj_id=".$order['ppj_id']." AND ppj_no=".$order['ppj_no'];
        
        $pl_data = $GLOBALS['db']->getRow($pl_sql);
        if($pl_data['end_time'] < time()){
            ecs_header('Location: ' . url('user/userbuy/ordererror'));
            exit();
        }
        
        $g_sql="SELECT * FROM ".$GLOBALS['ecs']->table('goods')."WHERE goods_id={$pl_data['goods_id']}";
        $goods_data=$GLOBALS['db']->getRow($g_sql);
        $this->assign('goods_data', $goods_data);


        $user_id=$this->user_id;
		
		
        $g_sql="SELECT * FROM ".$GLOBALS['ecs']->table('goods')."WHERE goods_id={$pl_data['goods_id']}";
        $goods_data=$GLOBALS['db']->getRow($g_sql);
        $this->assign('goods_data', $goods_data);

        
        $margin_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_seller_pay_margin')." WHERE order_sn=".$order['order_sn']." AND ls_pay_ok=1";
        $margin_data=$GLOBALS['db']->getRow($margin_sql);

        //单个买方出价信息
        $user_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_bid_user')." WHERE ppj_id=".$order['ppj_id']. " AND ppj_no=".$order['ppj_no']." AND user_id=".$user_id." AND is_status=2 AND spm_id={$margin_data['spm_id']}";       
        $user_bid=$GLOBALS['db']->getRow($user_sql);
        $this->assign('user_bid', $user_bid);

        $this->display();

    }

    
    //拍拍出价订单确认
    public function actionPaipaibuy()
    {
       
        $user_id=$this->user_id;
        $ppj_id=$_GET['ppj_id'];
        $ppj_no=$_GET['ppj_no'];
        
        $sell=$_GET['sell'];

        $at=$_GET['at'];

        $sell_id=substr($sell,0,strpos($sell, 'P1611'));
        $sell_price=substr($sell,strripos($sell,'P1611')+5)/100;
        $this->assign('sell_id', $sell_id);
        $this->assign('sell_price', $sell_price);

        //地址 
        $consignee = get_consignee($_SESSION['user_id']);
        
        if (!check_consignee_info($consignee, $flow_type) && $store_id <= 0) {
            ecs_header('Location: ' . url('address_list'));
            exit();
        }

        $user_address = get_order_user_address_list($user_id);
        if ($direct_shopping != 1 && !empty($user_id)) {
            $_SESSION['browse_trace'] = url('cart/index/index');
        }
        else {
            $_SESSION['browse_trace'] = url('flow/index/index');
        }

        if (count($user_address) <= 0 && $direct_shopping != 1 && $store_id <= 0) {
            ecs_header('Location: ' . url('address_list'));
            exit();
        }

        if ($consignee) {
            $consignee['province_name'] = get_goods_region_name($consignee['province']);
            $consignee['city_name'] = get_goods_region_name($consignee['city']);
            $consignee['district_name'] = get_goods_region_name($consignee['district']);
            $street = get_region_name($consignee['street']);
            $consignee['street_name'] = $street['region_name'];
            $consignee['region'] = $consignee['province_name'] . '&nbsp;' . $consignee['city_name'] . '&nbsp;' . $consignee['district_name'] . '&nbsp;' . $consignee['street_name'];
        }
        
        $default_id = $this->db->getOne('SELECT address_id FROM {pre}users WHERE user_id=\'' . $user_id . '\'');

        if ($consignee['address_id'] == $default_id) {
            $this->assign('is_default', '1');
        }

        $_SESSION['flow_consignee'] = $consignee;

        $this->assign('consignee', $consignee);


        $pl_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_list'). "WHERE ppj_id=".$ppj_id." AND ppj_no=".$ppj_no;
        $pl_data = $GLOBALS['db']->getRow($pl_sql);

        $goods_sql="SELECT * FROM".$GLOBALS['ecs']->table('goods')." WHERE goods_id=".$pl_data['goods_id'];
        $goods_data = $GLOBALS['db']->getRow($goods_sql);
        $this->assign('goods', $goods_data);

        $user_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_bid_user')." WHERE user_id=".$user_id." AND ppj_id=".$ppj_id." AND ppj_no=".$ppj_no." ORDER BY bid_id DESC LIMIT 1";
        $user_bid_data = $GLOBALS['db']->getRow($user_sql);
        $this->assign('user_bid', $user_bid_data);
        
        $user_order_sql="SELECT * FROM ".$GLOBALS['ecs']->table('order_info'). "WHERE user_id=".$user_id." AND ppj_id=".$ppj_id." AND ppj_no=".$ppj_no." AND goods_amount=".$pl_data['ppj_margin_fee']." AND pay_status='10' AND extension_code ='paipai_buy' ";
        $user_order_data = $GLOBALS['db']->getRow($user_order_sql);
        $this->assign('order', $user_order_data);
        
        $payment_list = available_payment_list(1, $cod_fee);

        if (isset($payment_list)) {
            foreach ($payment_list as $key => $payment) {
                if ($payment['is_cod'] == '1') {
                    $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
                }

                if (substr($payment['pay_code'], 0, 4) == 'pay_') {
                    unset($payment_list[$key]);
                    continue;
                }

                if ($payment['is_online'] == 1) {
                    unset($payment_list[$key]);
                }
                if ($payment['pay_code'] == 'balance') {
                    unset($payment_list[$key]);
                }

                if (!file_exists(ADDONS_PATH . 'payment/' . $payment['pay_code'] . '.php')) {
                    unset($payment_list[$key]);
                }

                if ($payment['pay_code'] == 'wxpay') {
                    if (!is_dir(APP_WECHAT_PATH)) {
                        unset($payment_list[$key]);
                    }

                    if (is_wechat_browser() == false && is_wxh5() == 0) {
                        unset($payment_list[$key]);
                    }
                }
            }
        }

        if($at){
            //更改卖家出价状态 买家状态
            $sel_pgs="SELECT * FROM dsc_paipai_goods_sellers WHERE user_id={$sell_id} AND ppj_id={$ppj_id} AND ppj_no={$ppj_no} AND ls_ok=1 ";
            $one_pgs_data=$GLOBALS['db']->getRow($sel_pgs);
            if($one_pgs_data){
                $up_pgs_sql="UPDATE dsc_paipai_goods_sellers SET ls_ok=0 WHERE user_id={$sell_id} AND ppj_id={$ppj_id} AND ppj_no={$ppj_no}";
                $GLOBALS['db']->query($up_pgs_sql);
                $up_pgs_sql="UPDATE dsc_paipai_goods_bid_user SET is_status=1 WHERE user_id={$user_id} AND ppj_id={$ppj_id} AND ppj_no={$ppj_no}";
                $GLOBALS['db']->query($up_pgs_sql);
                //添加匹配成功记录
                $sell_ok_data=array(
                    'user_id'=>$sell_id,
                    'buy_id'=>$user_id,
                    'goods_id'=>$goods_data['goods_id'],
                    'goods_id'=>$goods_data['goods_id'],
                    'ppj_id'=>$ppj_id,
                    'ppj_no'=>$ppj_no,
                    'sellers_fee'=>$one_pgs_data['seller_min_fee'],
                    'createtime'=>time()+8*3600,
                    'status'=>'0',
                    'spm_id'=>$user_bid_data['spm_id'],
                );
                $slleer_ok_id=$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('paipai_seller_ok'),$sell_ok_data,'INSERT');
            }else{
                $this->assign('match_status', '1');
            }
        }

        $this->assign('payment_list', $payment_list);

        if ($user_order_data['pay_id']) {
            $payment_selected = payment_info($user_order_data['pay_id']);

            if (file_exists(ADDONS_PATH . 'payment/' . $payment_selected['pay_code'] . '.php')) {
                $payment_selected['format_pay_fee'] = strpos($payment_selected['pay_fee'], '%') !== false ? $payment_selected['pay_fee'] : price_format($payment_selected['pay_fee'], false);
                $this->assign('payment_selected', $payment_selected);
            }
        }

        $this->assign('page_title', L('订单确认'));
        
        $this->display();
 
    }

    public function actionDone(){

        //货到付款
        $user_id=$this->user_id;
        $ppj_id=$_POST['ppj_id'];
        $ppj_no=$_POST['ppj_no'];
        $sell_id=$_POST['sell_id'];

        $match_price=$_POST['sell_price'];
        //拍拍活动信息
        $pl_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_list'). "WHERE ppj_id=".$ppj_id." AND ppj_no=".$ppj_no;
        $pl_data = $GLOBALS['db']->getRow($pl_sql);
        //获取商品的信息
        $goods_sql="SELECT pl.start_time,pl.end_time,pl.ppj_startpay_time,pl.ppj_endpay_time,pl.ppj_staus,pl.goods_count,pl.ppj_start_fee,pl.ppj_buy_fee,pl.goods_count,g.goods_id,g.goods_name,g.market_price,g.shop_price,g.cost_price,g.goods_sn FROM ".$GLOBALS['ecs']->table('paipai_list')." AS pl LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON pl.goods_id=g.goods_id WHERE pl.ppj_id={$ppj_id} AND pl.ppj_no={$ppj_no} ";
        $goods_data=$GLOBALS['db']->getRow($goods_sql);

        //买家出价信息
        $user_bid_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_bid_user')." WHERE user_id=".$user_id." AND ppj_id=".$ppj_id." AND ppj_no=".$ppj_no." AND is_status=1";
        $user_bid_data = $GLOBALS['db']->getRow($user_bid_sql);
        //卖家出价信息
        $sell_sql=" SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_sellers')." WHERE user_id={$sell_id} "." AND ppj_no=".$ppj_no." AND ls_ok=0";
        $sell_news=$GLOBALS['db']->getRow($sell_sql);
        //成交记录表
        $sell_ok_sql=" SELECT * FROM ".$GLOBALS['ecs']->table('paipai_seller_ok')." WHERE user_id={$sell_news['user_id']} AND buy_id={$user_id} "." AND ppj_id=".$ppj_id." AND ppj_no=".$ppj_no;
        $sell_ok_data=$GLOBALS['db']->getRow($sell_ok_sql);



//        if($sell_news){
//            //卖家利润
//            $income=number_format(($sell_news['seller_min_fee']-$goods_data['cost_price'])*0.8,2);
//
//            $sell_ok_data=array(
//                'user_id' => $sell_news['user_id'],
//                'buy_id' => $user_id,
//                'goods_id' => $goods_data['goods_id'],
//                'ppj_id' => $ppj_id,
//                'ppj_no' => $ppj_no,
//                'sellers_fee' => $user_bid_data['bid_price'],
//                'goods_nowprice' => $user_bid_data['bid_price'],
//                'createtime' => time()+8*3600,
//                'status' => 1,   // 货到付款（系统默认为已付款）
//                'getmoney' => $income,
//                'spm_id' => $user_bid_data['spm_id']
//            );
//            //匹配成功数据加入卖家数据表
//            $sell_ok_time=time();
//            $slleer_ok_id=$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('paipai_seller_ok'),$sell_ok_data,'INSERT');
//
//        }

        $user_order_sql="SELECT * FROM ".$GLOBALS['ecs']->table('order_info'). "WHERE user_id=".$user_id." AND ppj_id=".$ppj_id." AND ppj_no=".$ppj_no." AND goods_amount=".$pl_data['ppj_margin_fee']." AND pay_status='10' AND extension_code ='paipai_buy' ";
        $user_order_data = $GLOBALS['db']->getRow($user_order_sql);

        $order_data=array(
                'order_sn'=> get_order_sn(),
                'user_id' => $user_bid_data['user_id'],
                'order_status' => '1',
                'pay_status' => '2',
                'consignee' => $user_order_data['consignee'],
                'country' => $user_order_data['country'],
                'province' => $user_order_data['province'],
                'city' => $user_order_data['city'],
                'district' => $user_order_data['district'],
                'street' => $user_order_data['street'],
                'address' => $user_order_data['address'],
                'mobile' => $user_order_data['mobile'],
                'email' => $user_order_data['email'],
                'shipping_id' => $user_order_data['shipping_id'],
                'shipping_name' => $user_order_data['shipping_name'],
                'shipping_code' => $user_order_data['shipping_code'],
                'shipping_type' => $user_order_data['shipping_type'],
                'inv_type' => '0',
                'point_id' => '0',
                'pay_id' => $user_order_data['pay_id'],
                'how_oos' =>  $user_order_data['how_oos'],
                'goods_amount' => $user_bid_data['bid_price'],
                'order_amount' => $user_bid_data['bid_price'],
                'referer' => $user_order_data['referer'],
                'add_time' => time()+8*3600,
                'extension_code' => 'paipai_buy',
                'extension_id' => $goods_data['goods_id'],
                'ppj_id' => $user_order_data['ppj_id'],
                'ppj_no' => $user_order_data['ppj_no'],
        );
        if (0 < $order_data['pay_id']) {
            $payment = payment_info($order_data['pay_id']);
            $order_data['pay_name'] = addslashes($payment['pay_name']);

        }

        $order_insert=$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'),$order_data,'INSERT');
        if($order_insert){

            $order_id_sql="SELECT order_id FROM ".$GLOBALS['ecs']->table('order_info'). "WHERE order_sn=".$order_data['order_sn'];
            $order_id=$GLOBALS['db']->getRow($order_id_sql);

            $order_goods_data=array(
                'order_id'=> $order_id['order_id'],
                'user_id'=> $user_bid_data['user_id'],
                'goods_id'=> $goods_data['goods_id'],
                'goods_name'=>$goods_data['goods_name'] ,
                'goods_sn'=> $goods_data['goods_sn'],
                'goods_number'=> '1',
                'market_price'=> $goods_data['market_price'],
                'goods_price'=> $goods_data['goods_price'],
                'area_id'=> '24',
                'warehouse_id'=>'2' ,
                'ppj_no'=>$user_order_data['ppj_no']
            );
            $order_goods_insert=$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_goods'),$order_goods_data,'INSERT');
            if($order_goods_insert){
                $goods_logs_data=array(
                    'order_id'=> $order_id['order_id'],
                    'goods_id'=> $goods_data['goods_id'],
                    'use_storage'=> '1',
                    'number'=>'-1' ,
                    'add_time'=> time()+8*3600,
                );
                $log_res =$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'),$goods_logs_data,'INSERT');

                $up_so_sql="UPDATE dsc_paipai_seller_ok SET order_id={$order_id['order_id']} WHERE ok_id=".$sell_ok_data['ok_id'];
                $up_so_res=$GLOBALS['db']->query($up_so_sql);
            }
            if($log_res && $up_so_res){
                //送券三张
                $time=date('Y-m-d',time()+8*3600);
                $endtime=strtotime(date('Y-m-d',strtotime("$time + 1 month")))+15*3600;

                $sql="SELECT * FROM dsc_paipai_seller WHERE user_id=".$user_bid_data['user_id']." AND goods_id=".$order_id['order_id']." AND beizhu='购买赠送' GROUP BY seller_id DESC LIMIT 1 ";
                $ticket_last=$GLOBALS['db']->getRow($sql);

                $insert_data=array(
                    'goods_id'=> $pl_data['goods_id'],
                    'createtime' => strtotime($time),
                    'usestaus' => 0,
                    'user_id' => $user_bid_data['user_id'],
                    'endtime' => $endtime,
                    'beizhu' => '购买赠送',
                );

                if(empty($ticket_last)){
                    for($i=0;$i<3;$i++){
                        $insert_data['ppj_no']=$order_data['ppj_no']+$i+1;
                        if($i <= 1){
                            $GLOBALS['db']->autoExecute('dsc_paipai_seller', $insert_data, 'INSERT');
                        }else if($i == 2){
                            $insert_data['active']='1';
                            $GLOBALS['db']->autoExecute('dsc_paipai_seller', $insert_data, 'INSERT');
                        }
                    }
                }else{
                    for($i=$ticket_last['ppj_no'];$i<$ticket_last['ppj_no']+3;$i++){
                        $insert_data['ppj_no']=$i+1;
                        if($i <= $ticket_last['ppj_no']+1){
                            $GLOBALS['db']->autoExecute('dsc_paipai_seller', $insert_data, 'INSERT');
                        }else if($i == $ticket_last['ppj_no']+2){
                            $insert_data['active']='1';
                            $GLOBALS['db']->autoExecute('dsc_paipai_seller', $insert_data, 'INSERT');
                        }
                    }
                }

                //拍拍活动库存-1
                $up_pl_sql="UPDATE dsc_paipai_list SET goods_count=goods_count-1 WHERE ppj_id=".$user_order_data['ppj_id']." AND ppj_no=".$user_order_data['ppj_no'];
                $pl_res=$GLOBALS['db']->query($up_pl_sql);
                if($pl_res){
                    ecs_header('Location: ' . url('user/index/index') . "\n");
                }
            }

//            if (0 < $order_data['order_amount']  && $payment['pay_code'] != 'cod') {
//
//                ecs_header('Location: ' . url('onlinepay/index/index', array('order_sn' => $order_data['order_sn'])) . "\n");
//            }
        }

        $this->display();

    }


	
	public function actionPbuy(){
		
		$ppj_id=$_REQUEST['pjd'];
    	$ppj_no=$_REQUEST['pjn'];
    	$where = ' WHERE 1 ';
    	$user_id=$this->user_id;
        $goods_sql="SELECT pl.start_time,pl.end_time,pl.ppj_margin_fee,pl.ext_info,pl.ppj_startpay_time,pl.ppj_endpay_time,pl.ppj_staus,pl.goods_count,pl.ppj_start_fee,pl.ppj_buy_fee,pl.goods_count,g.goods_id,g.market_price,g.shop_price,g.cost_price FROM ".$GLOBALS['ecs']->table('paipai_list')." AS pl LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON pl.goods_id=g.goods_id WHERE pl.ppj_id={$ppj_id} AND pl.ppj_no={$ppj_no} ";
  	    $goods_data=$GLOBALS['db']->getRow($goods_sql);  

        if($goods_data['goods_count'] == '0'){
            exit(json_encode(array('match_bid' => 3)));
        }

		$where.=" AND ppj_id={$ppj_id} AND ppj_no={$ppj_no}";

        //单个买方出价信息
  		$user_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_bid_user')." WHERE ppj_id={$ppj_id} AND ppj_no={$ppj_no}"." AND user_id=".$user_id." AND is_status=2";
  	    $user_bid=$GLOBALS['db']->getRow($user_sql);

        if($user_bid['bid_price'] < $goods_data['shop_price'] && $user_bid['bid_price'] > $goods_data['ppj_buy_fee']  ){
            //所有买方出价信息
			$all_user_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_bid_user').$where." AND user_id !=".$user_id." AND is_status=2 AND rt_auto=0 ";

			$all_user_bid=$GLOBALS['db']->getAll($all_user_sql);

			if(empty($all_user_bid)){
				$bid_match='1';
			}else{
				foreach($all_user_bid as $key=> $value){
                $max_fee_array[]=$value['bid_price'];
				}
				$max_bid=max($max_fee_array);

				if($max_bid == $user_bid['bid_price'] ){
					foreach($all_user_bid as $key=> $value){
						if($value['bid_time'] < $user_bid['bid_time']){
							$bid_match='1';       //出价最高者
						}else{
							exit(json_encode(array('match_bid' => 2)));
						}
					}
				}elseif( $user_bid['bid_price'] > $max_bid ){
						$bid_match='1';       //出价最高者
				}else{
						exit(json_encode(array('match_bid' => 2)));
				}
			}
            if($bid_match == '1'){
                //卖方
				$sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_sellers') .$where.' AND user_id!='.$user_id.' AND ls_ok=1';
			    $seller_data=$GLOBALS['db']->getAll($sql);
				if(empty($seller_data)){
                    $stat = paipai_buy_stat($ppj_id, $ppj_id,$goods_data['ppj_margin_fee']);//获取订单数
                    $cur_amount = $stat['valid_order'];
                    if($cur_amount>=0){
                        $ext_info = unserialize($goods_data['ext_info']);
                        $price_ladder = $ext_info['price_ladder'];
                        foreach ($price_ladder as $plkey => $amount_price) {
                            if ($amount_price['amount'] <= $cur_amount) {
                                $cur_price = $amount_price['price'];
                            }
                            else if( $cur_amount == 0 ) {
                                $cur_price=$amount_price['price'];
                                break;
                            }
                        }
                        $us_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_sellers') .$where." AND user_id='3331' AND ls_ok=1";
                        $us_data=$GLOBALS['db']->getRow($us_sql);
                        if(!$us_data){
                            $goods_seller_data=array(
                                'user_id'=>'3331',
                                'ppj_id'=>$ppj_id,
                                'ppj_no'=>$ppj_no,
                                'seller_min_fee'=>$cur_price,
                                'seller_max_fee'=>$cur_price+20,
                                'ls_ok'=>'1',
                                'ls_staus'=>'1',
                                'createtime'=>time()+8*3600
                            );
                            $sell_res=$GLOBALS['db']->autoExecute('dsc_paipai_goods_sellers', $goods_seller_data, 'INSERT');
                        }
                        if($sell_res || $us_data){
                            //卖方
                            $sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_sellers') .$where.' AND user_id!='.$user_id.' AND ls_ok=1';
                            $seller_data=$GLOBALS['db']->getAll($sql);
                        }else{
                            exit(json_encode(array('match_bid' => 2)));
                        }
                    }else{
                        exit(json_encode(array('match_bid' => 2)));
                    }
			    }

		        foreach($seller_data as $key=>$val){
			            $min_fee_arr[]=$val['seller_min_fee'];
			            $sell_id[]=$val['user_id'];
				}
                $sell_min_fee=min($min_fee_arr);

				if($sell_min_fee < $user_bid['bid_price'] ){

					$sell_sql=" SELECT * FROM ".$GLOBALS['ecs']->table('paipai_goods_sellers').$where." AND user_id={$sell_id[0]}";
	                $sell_news=$GLOBALS['db']->getRow($sell_sql);
                    if($sell_news){
                        $sell=$sell_id[0].'P1611'.($sell_min_fee)*100;
                        echo json_encode(array('match_bid'=>1,'sell'=>$sell));  //匹配成功
                    }
                }else{
                        echo json_encode(array('match_bid'=>2));  //匹配失败
                }
            }

        }else{
                echo json_encode(array('match_bid'=>4));  //出价失败
        }
        
        
    }




    public function actionBuyerror(){

        $this->display();

    }

    public function actionOrdererror(){

        $this->display();

    }



























}
?>