<?php
//zend by 多点乐  禁止倒卖 一经发现停止任何服务
namespace App\Modules\Groupbuy\Controllers;

class IndexController extends \App\Modules\Base\Controllers\FrontendController
{
	protected $user_id = 0;
	protected $goods_id = 0;
	protected $groupbuyid = 0;
	protected $region_id = 0;
	protected $area_info = array();
	protected $size = 10;

	public function __construct()
	{
		parent::__construct();
		
		$this->init_params();
		
		L(require LANG_PATH . C('shop.lang') . '/other.php');
		 
		$this->area_id = $this->area_info['region_id'];
	}


	//显示进行中的拍拍活动
	public function actionUnderway(){

		$now = time()+8*2600;
		$user_id = $_SESSION['user_id'];
		
		$default_sort_order_method = C('sort_order_method') == '0' ? 'ASC' : 'DESC';

		if ($_REQUEST['sort'] == 'comments_number') {
			$default_sort_order_type = C('sort_order_type') == '0' ? 'start_time' : (C('sort_order_type') == '1' ? 'shop_price' : 'last_update');
		}
		else {
			$default_sort_order_type = 'ppj_id';
		}
		
		$this->sort = isset($_REQUEST['sort']) && in_array(trim(strtolower($_REQUEST['sort'])), array('ppj_id', 'start_time', 'sales_volume', 'comments_number')) ? trim($_REQUEST['sort']) : $default_sort_order_type;


		$this->order = isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')) ? trim($_REQUEST['order']) : $default_sort_order_method;

		$page = I('post.page', 1, 'intval');

		$keywords = I('keyword');

		$status='underway';
		$count = group_buy_count($keywords,$status);

		$max_page = 0 < $count ? ceil($count / $this->size) : 1;

		if ($max_page < $page) {
			$page = $max_page;
		}
		$this->assign('total_pages',$max_page);

		$gb_list = paipai_underway_list($keywords, $this->sort, $this->order);

		foreach($gb_list as $k => $val){
								
			$ext_info = unserialize($val['ext_info']);

			$val = array_merge($val, $ext_info);


			$val['formated_end_date'] = groupbuydate($val['end_date']);
			$val['formated_start_date'] = groupbuydate($val['start_date']);

			//$val['is_end'] = $val['end_date'] < $now ? 1 : 0;

			if($val['end_date'] > $now && $val['start_date'] < $now){

				$val['is_end'] = 1;

			}else if($val['start_date'] > $now){

				$val['is_end'] = 0;

			}else{
				$val['is_end'] = 2;
			}



			$val['formated_deposit'] = price_format($val['ppj_margin_fee'], false);


			$price_ladder = $val['price_ladder'];
		
		
			if (!is_array($price_ladder) || empty($price_ladder)) {

				$price_ladder = array(
					array('amount' => 0, 'price' => 0)
					);
			}
			else {
				foreach ($price_ladder as $key => $amount_price) {

					$price_ladder[$key]['formated_price'] = price_format($amount_price['price']);

				}
			}

			$val['price_ladder'] = $price_ladder;

			$price = $val['market_price'];

			$nowprice = $val['price_ladder'][0]['price'];



			$stat = paipai_buy_stat($val['ppj_id'], $val['ppj_no'],$val['ppj_margin_fee']);//获取订单数

			$val = array_merge($val, $stat);

			$cur_amount = $stat['valid_order'];
		

			foreach ($price_ladder as $key => $amount_price) {

				if ($amount_price['amount'] <= $cur_amount) {

					$cur_price = $amount_price['price'];

				}
				else {

					$cur_price=0;
					break;
				}
			}

			$val['cur_amount'] = $cur_amount;

			$val['goods_thumb'] = get_image_path($val['goods_thumb']);

			$val['url'] = build_uri('groupbuy', array('gbid' => $val['group_buy_id']));


			$val['cur_price'] = $cur_price;


			$val['price'] = price_format($cur_price, false);// 当前价格


					//遍历报名表
			$sqls='select * from '. $GLOBALS['ecs']->table('paipai_goods_sellers') .'where user_id='.$user_id.' and ppj_id='.$val['ppj_id'].' and ppj_no='.$val['ppj_no'];

			$baoming = $GLOBALS['db']->getRow($sqls);

			if(empty($baoming))
			{
				$is_baoming =0; // 未报名，未出价
			}
			else if($baoming['ls_ok']==1&&$baoming['ls_staus']==0)
			{
				$is_baoming =1; // 已手动出过一次价，但未匹配成功      	//已设置应该是设置了自动售出。手动的话就是已经直接售出了   没有设置一说

			}
			else if($baoming['ls_ok']==1&&$baoming['ls_staus']==1)
			{
				$is_baoming =2; // 已设置过自动出价   				//

			}else if($baoming['ls_ok']==0)
			{
				$is_baoming =3 ;//此单 卖家已过成功一单
			}

			 $val['is_baoming']=$is_baoming;

			$u_sql="SELECT seller_max_fee FROM ".$GLOBALS['ecs']->table('paipai_goods_sellers')."WHERE user_id=".$user_id." AND ppj_id=".$val['ppj_id']." AND ppj_no=".$val['ppj_no'];
			$gs_data = $GLOBALS['db']->getOne($u_sql);
			$u_sql1="SELECT seller_min_fee FROM ".$GLOBALS['ecs']->table('paipai_goods_sellers')."WHERE user_id=".$user_id." AND ppj_id=".$val['ppj_id']." AND ppj_no=".$val['ppj_no'];
			$gs_data1 = $GLOBALS['db']->getOne($u_sql1);

			$val['seller_min_fee']=$gs_data1;
			$val['seller_max_fee']=$gs_data;
			$group_buy[] = $val;
	    }
		$this->assign('att',$group_buy);

		$this->display();
			
	}
			
				
	//显示未开始的方法
	public function actionNotstarted(){
		
		$now = time()+8*3600;
		$user_id = $_SESSION['user_id'];
				
		$default_sort_order_method = C('sort_order_method') == '0' ? 'ASC' : 'DESC';

		if ($_REQUEST['sort'] == 'comments_number') {
			$default_sort_order_type = C('sort_order_type') == '0' ? 'start_time' : (C('sort_order_type') == '1' ? 'shop_price' : 'last_update');
		}
		else {
			$default_sort_order_type = 'ppj_id';
		}
		
		$this->sort = isset($_REQUEST['sort']) && in_array(trim(strtolower($_REQUEST['sort'])), array('ppj_id', 'start_time', 'sales_volume', 'comments_number')) ? trim($_REQUEST['sort']) : $default_sort_order_type;


		$this->order = isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')) ? trim($_REQUEST['order']) : $default_sort_order_method;

		$page = I('post.page', 1, 'intval');

		$keywords = I('keyword');

		$status='nostarted';
		$count = group_buy_count($keywords,$status);

		$max_page = 0 < $count ? ceil($count / $this->size) : 1;

		if ($max_page < $page) {
			$page = $max_page;
		}

		$gb_list = paipai_buy_add_list($this->size, $page, $keywords, $this->sort, $this->order);
 

		foreach ($gb_list as $key => $val) {
		
		
			$ext_info = unserialize($val['ext_info']);

			$val = array_merge($val, $ext_info);


			$val['formated_end_date'] = groupbuydate($val['end_date']);
			$val['formated_start_date'] = groupbuydate($val['start_date']);


			//$val['is_end'] = $val['end_date'] < $now ? 1 : 0;

			if($val['end_date'] > $now && $val['start_date'] < $now){

				$val['is_end'] = 1;

			}else if($val['start_date'] > $now){

				$val['is_end'] = 0;

			}else{
				$val['is_end'] = 2;
			}



			$val['formated_deposit'] = price_format($val['ppj_margin_fee'], false);


			$price_ladder = $val['price_ladder'];


			if (!is_array($price_ladder) || empty($price_ladder)) {

				$price_ladder = array(
					array('amount' => 0, 'price' => 0)
					);
			}
			else {
				foreach ($price_ladder as $key => $amount_price) {

					$price_ladder[$key]['formated_price'] = price_format($amount_price['price']);

				}
			}

			$val['price_ladder'] = $price_ladder;

			$price = $val['market_price'];

			$nowprice = $val['price_ladder'][0]['price'];



			$stat = paipai_buy_stat($val['ppj_id'], $val['ppj_no'],$val['ppj_margin_fee']);//获取订单数

			$val = array_merge($val, $stat);

			$cur_amount = $stat['valid_order'];


			foreach ($price_ladder as $key => $amount_price) {

				if ($amount_price['amount'] <= $cur_amount) {

					$cur_price = $amount_price['price'];

				}
				else {

					$cur_price=0;
					break;
				}
			}


			$val['cur_amount'] = $cur_amount;


			$val['goods_thumb'] = get_image_path($val['goods_thumb']);

			$val['url'] = build_uri('groupbuy', array('gbid' => $val['group_buy_id']));


			$val['cur_price'] = $cur_price;


			$val['price'] = price_format($cur_price, false);// 当前价格

			//遍历报名表
			$sqls='select * from '. $GLOBALS['ecs']->table('paipai_goods_sellers') .'where user_id='.$user_id.' and ppj_id='.$val['ppj_id'].' and ppj_no='.$val['ppj_no'];

			$baoming = $GLOBALS['db']->getRow($sqls);

			if(empty($baoming)){
				$is_baoming =0;
			}else{
				$is_baoming =1;
			}

			$val['is_baoming']=$is_baoming;

			$group_buy[] = $val;
						
	    }
			
	$this->assign('att',$group_buy);
		
	$this->display();

	}
	
	

	//参加报名的方法
	public function actionBaoming(){

		$user_id = $_SESSION['user_id'];
		$ppj_id = $_POST['ppj_id'];
		$ppj_no = $_POST['ppj_no'];

		$seller_max_fee = $_POST['max'];
		$seller_min_fee = $_POST['min'];
		$time = time();
		
		if(empty($seller_max_fee) && empty($seller_min_fee)){
			echo json_encode(array('re'=>4)); exit;   //没有优惠券	
		}

		//查询拍拍街的期数和商品id
		$sql1 = "select * from dsc_paipai_list where ppj_id = {$ppj_id} and ppj_no = {$ppj_no}";
		$date = $GLOBALS['db']->getRow($sql1);
       
		//查询有没有匹配的优惠券
		$sql2 = "select * from dsc_paipai_seller where goods_id = {$date['goods_id']} and user_id = {$user_id} and usestaus = 1";
		$result = $GLOBALS['db']->getAll($sql2);       
			
		if(empty($result)){
			echo json_encode(array('re'=>3)); exit;   //没有优惠券	
		}
        
		//查询goods商品的最低价格
		$sql3 = "select * from dsc_goods where goods_id = {$date['goods_id']} ";
		$result3 = $GLOBALS['db']->getRow($sql3);
		
	    //判断价格是否合理
		if($seller_min_fee > $seller_max_fee){
			echo json_encode(array('re'=>4)); exit;   //价格不价格合理 
		}
		if($seller_min_fee < $result3['cost_price'] || $seller_min_fee > $result3['shop_price'] ){
			echo json_encode(array('re'=>4)); exit;   //价格不价格合理 
		}
		if($seller_max_fee < $result3['cost_price'] || $seller_max_fee > $result3['shop_price']){
			echo json_encode(array('re'=>4)); exit;   //价格不价格合理 
		}
		
				
		foreach($result as $ke=>$va){
			
			if($va['ppj_no'] == 0 && $va['endtime'] > $time){// 期数为0 且结束时间大于当前时间 为可用
				$success_data=1;
			}else if($va['ppj_no'] >= $ppj_no && $va['endtime'] > $time){  //购买赠送期数大于当前期 且结束时间大于当前时间 为可用
				$success_data=1;
			}else{
				echo json_encode(array('re'=>5)); exit;  //期数不可用
			}
			if($success_data ==1 ){
								
                $gs_ins_data=array(
				    'ppj_id'=>$ppj_id,
					'user_id'=>$user_id,
					'ppj_no'=>$date['ppj_no'],
					'seller_max_fee'=>$seller_max_fee,
					'seller_min_fee'=>$seller_min_fee,
					'ls_ok'=>1,
					'ls_staus'=>1,
					'createtime'=>$time
				);	
				
				$sql_shoudong = "select * from dsc_paipai_goods_sellers where user_id = {$user_id} and ppj_id = {$ppj_id} and ppj_no = {$ppj_no}";
				$is_shoudong = $GLOBALS['db']->getRow($sql_shoudong);
//				echo json_encode(array('re'=>$is_shoudong)); exit;
				if($is_shoudong){
//					echo json_encode(array('re'=>1)); exit;
					$sql_suc = "update dsc_paipai_goods_sellers set ls_staus = 1,seller_max_fee = {$seller_max_fee},seller_min_fee = {$seller_min_fee} where ppjs_id = {$is_shoudong['ppjs_id']}";
//					echo json_encode(array('re'=>$sql_suc)); exit;
					$ins_suc = $GLOBALS['db']->query($sql_suc);
					if($ins_suc){
						echo json_encode(array('re'=>1)); exit;
					}else{
						echo json_encode(array('re'=>2)); exit;
					}
				}else{
//					echo json_encode(array('re'=>2)); exit;
					//插入报名信息				
	                $ins_suc =$GLOBALS['db']->autoExecute('dsc_paipai_goods_sellers', $gs_ins_data, 'INSERT');
					if($ins_suc){
						$sqls = "update dsc_paipai_seller set usestaus = 1 where seller_id = {$va['seller_id']}";
					    $GLOBALS['db']->query($sqls);
						echo json_encode(array('re'=>1)); exit;
					}else{
						echo json_encode(array('re'=>2)); exit;
					}
				}

			}
		}

	}
	
    //买家出价
	public function actionChujia(){
		$id = $_SESSION['user_id'];
		$ppj_id = $_POST['ppj_id'];
		$ppj_no = $_POST['ppj_no'];
		$max = $_POST['max'];
		$shop_price = $_POST['shop_price'];
		
		if($shop_price < $max || $max==0){
			echo json_encode(array('re'=>3));  exit;
		}
		$time = time();
		$spm_sql="SELECT * FROM dsc_paipai_seller_pay_margin WHERE ppj_id = {$ppj_id} and user_id = {$id} and ppj_no = {$ppj_no} ORDER BY spm_id DESC LIMIT 1";
		$spm_data = $GLOBALS['db']->getRow($spm_sql);
	    
		$sql = "UPDATE dsc_paipai_goods_bid_user SET bid_price = {$max},bid_time = {$time} where ppj_id = {$ppj_id} and user_id = {$id} and ppj_no = {$ppj_no} and is_status =2 AND spm_id={$spm_data['spm_id']}";
		$success = $GLOBALS['db']->query($sql);
		if($success > 0){
			echo json_encode(array('re'=>1));
		}else{
			echo json_encode(array('re'=>2));			
		}
	}
	
	
	//拍拍小店卖家重新出价
	public function actionNewprice(){

		$id = $_SESSION['user_id'];
		$ppj_id = $_POST['ppj_id'];
		$ppj_no = $_POST['ppj_no'];
		
		//  $ppj_id = 109;
		$seller_max_fee = $_POST['max'];
		$seller_min_fee = $_POST['min'];
		//  $seller_max_fee = 1000;
		$time = time();
		
		$arr['user_id']=$id;
		$arr['ppj_id']=$ppj_id;
		$arr['ppj_no']=$ppj_no;
		$arr['seller_max_fee']=$seller_max_fee;
		$arr['seller_min_fee']=$seller_min_fee;
		
		//插入报名信息
		$success = 0;
		$sql1 = "UPDATE dsc_paipai_goods_sellers SET seller_max_fee = {$arr['seller_max_fee']},seller_min_fee = {$arr['seller_min_fee']} WHERE ppj_id = {$arr['ppj_id']} and user_id = {$arr['user_id']} and ppj_no = {$arr['ppj_no']}";
		$success = $GLOBALS['db']->query($sql1);
//		echo json_encode(array('re'=>$success));exit;
		if($success>0){
			echo json_encode(array('re'=>1));
		}else{
			echo json_encode(array('re'=>2));
		}
	}


	//显示我报名的
	public function actionSign()
	{
		$id = $_SESSION['user_id'];
		$time = time();
		
		//查询我报名的表
		$sql = "select ppj_id,ppj_no,createtime from dsc_paipai_goods_sellers where user_id={$id}";
		$re = $GLOBALS['db']->getAll($sql);
		foreach($re as $k=>$v){
//			$arr['baoming'] = date('Y-m-d H:i:s',$v['createtime']);
			$sql1 = "select * from dsc_paipai_list where ppj_id = {$v['ppj_id']} and ppj_no = {$v['ppj_no']}";
			$res = $GLOBALS['db']->query($sql1);
			foreach($res as $r=>$e){
				$res['baoming'] = date('Y-m-d H:i:s',$v['createtime']+8*60*60);
				$sql2 = "select * from dsc_goods where goods_id = {$e['goods_id']}";
				$resu = $GLOBALS['db']->query($sql2);
				foreach($resu as $l => $a){
					$arr[] = array_merge($a,$res);
				}
			}
		}
		
		foreach($arr as $key =>$val ){
			$arr[$key]['goods_thumb'] = __STATIC__.'/'.$val['goods_thumb'];
			$arr[$key]['end_time'] = $val[0]['end_time'];
			$arr[$key]['start_time'] = $val[0]['start_time'];
			if($arr[$key]['end_time'] > $time && $arr[$key]['start_time'] < $time){
				$arr[$key]['is_end'] = 1;
			}else if($arr[$key]['start_time'] > $time){
				$arr[$key]['is_end'] = 0;
			}else{
				$arr[$key]['is_end'] = 2;
			}
			$arr[$key]['end_date'] = floor(($val[0]['end_time']-$time)/86400);
			$arr[$key]['start_date'] = floor(($time-$val[0]['start_time'])/86400);
			$arr[$key]['url'] = __STATIC__.'/mobile/index.php?m=groupbuy&a=detail&id='.$val[0]['ppj_id'];
			$this->groupbuyid = $val[0]['ppj_id'];
			$arr[$key]['end_date'] = floor(($val[0]['end_time']-$time)/86400);
			$group = paipai_buy_info($this->groupbuyid);
			$arr[$key]['formated_cur_price'] = $group['formated_cur_price'];
			//遍历报名表
			$sqls='select * from '. $GLOBALS['ecs']->table('paipai_goods_sellers') .'where user_id='.$_SESSION['user_id'].' and ppj_id='.$arr[$key][0]['ppj_id'].' and ppj_no='.$arr[$key][0]['ppj_no'];	
			$baoming = $GLOBALS['db']->getRow($sqls);
			if(empty($baoming))
			{
				$is_baoming =0; // 未报名，未出价
			}
			else if($baoming['ls_ok']==1 && $baoming['ls_staus']==0)
			{
				$is_baoming =1; // 已手动出过一次价，但未匹配成功      	//已设置应该是设置了自动售出。手动的话就是已经直接售出了   没有设置一说
			}
			else if($baoming['ls_ok']==1 && $baoming['ls_staus']==1)
			{
				$is_baoming =2; // 已设置过自动出价   				//
			}else if($baoming['ls_ok']==0)
			{
				$is_baoming =3; //此单卖家已过成功一单
			}
			$val['is_baoming']=$is_baoming; 
			$arr[$key]['is_baoming'] = $val['is_baoming'];

			$u_sql="SELECT seller_max_fee FROM ".$GLOBALS['ecs']->table('paipai_goods_sellers')."WHERE user_id=".$id." AND ppj_id=".$val[0]['ppj_id']." AND ppj_no=".$val[0]['ppj_no'];
		    $gs_data = $GLOBALS['db']->getOne($u_sql);
		    $u_sql1="SELECT seller_min_fee FROM ".$GLOBALS['ecs']->table('paipai_goods_sellers')."WHERE user_id=".$id." AND ppj_id=".$val[0]['ppj_id']." AND ppj_no=".$val[0]['ppj_no'];
		    $gs_data1 = $GLOBALS['db']->getOne($u_sql1);
//		    
	    	$v['seller_min_fee']=$gs_data1;
	    	$v['seller_max_fee']=$gs_data;
//	    	var_dump($val['seller_max_fee']); 
			$arr[$key]['seller_max_fee'] = $v['seller_max_fee'];
			$arr[$key]['seller_min_fee'] = $v['seller_min_fee'];
		}
//		var_dump($arr);
		$this->assign('att',$arr);
		$this->display();
	}


	public function actionIndex()
	{	
		$default_sort_order_method = C('sort_order_method') == '0' ? 'ASC' : 'DESC';

		if ($_REQUEST['sort'] == 'comments_number') {
			$default_sort_order_type = C('sort_order_type') == '0' ? 'start_time' : (C('sort_order_type') == '1' ? 'shop_price' : 'last_update');
		}
		else {
			$default_sort_order_type = 'ppj_id';
		}

		if (IS_AJAX) {
			
			$this->sort = isset($_REQUEST['sort']) && in_array(trim(strtolower($_REQUEST['sort'])), array('ppj_id', 'start_time', 'sales_volume', 'comments_number')) ? trim($_REQUEST['sort']) : $default_sort_order_type;
			
		
			$this->order = isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')) ? trim($_REQUEST['order']) : $default_sort_order_method;
			
			$page = I('post.page', 1, 'intval');
			
			$keywords = I('keyword');

			$status='underway';
			$count = group_buy_count($keywords,$status);
			$max_page = 0 < $count ? ceil($count / $this->size) : 1;

			if ($max_page < $page) {
				$page = $max_page;
			}

			$gb_list = paipai_buy_list($this->size, $page, $keywords, $this->sort, $this->order);
			exit(json_encode(array('gb_list' => $gb_list, 'totalPage' => ceil($count / $this->size))));

		}

		$seo = get_seo_words('group');
		foreach ($seo as $key => $value) {
			$seo[$key] = html_in(str_replace(array('{sitename}', '{key}', '{description}'), array(C('shop.shop_name'), C('shop.shop_keywords'), C('shop.shop_desc')), $value));
		}
		$page_title = !empty($seo['title']) ? $seo['title'] : L('group_purchase_index');		
		$keywords = !empty($seo['keywords']) ? $seo['keywords'] : C('shop.shop_keywords');	
		$description = !empty($seo['description']) ? $seo['description'] : C('shop.shop_desc');		
		$share_data = array('title' => $page_title, 'desc' => $description, 'link' => '', 'img' => '');
		
		$this->assign('share_data', $this->get_wechat_share_content($share_data));		
		$this->assign('page_title', $page_title);		
		$this->assign('keywords', $keywords);		
		$this->assign('description', $description);		
				
		$this->display();
	}
	
	
	
	// 拍拍商品详情  86--301
	
	public function actionDetail()
	{		
		$this->groupbuyid = I('id');

		if (!$this->groupbuyid) {
			ecs_header("Location: ./\n");
		}
		
		//var_dump($this->groupbuyid);
		$group_buy = paipai_buy_info($this->groupbuyid);

		if (empty($group_buy)) {
			ecs_header("Location: ./\n");
			exit();
		}

		$group_buy['gmt_end_date'] = $group_buy['end_date'];

		
		
		$this->assign('group_buy', $group_buy);
		
		$this->assign('group_buy_id', $this->groupbuyid);
		
		$first_month_day = local_mktime(0, 0, 0, date('m'), 1, date('Y'));
		
		$last_month_day = local_mktime(0, 0, 0, date('m'), date('t'), date('Y')) + 24 * 60 * 60 - 1;
		
		$group_list = get_month_day_start_end_goods($this->groupbuyid, $first_month_day, $last_month_day);
		
		$this->assign('group_list', $group_list);
		
		$merchant_group = get_merchant_group_goods($this->groupbuyid);
		
		$this->assign('merchant_group_goods', $merchant_group);
		
		$this->assign('look_top', get_top_group_goods('click_count'));
		
		$this->assign('buy_top', get_top_group_goods('sales_volume'));
		
		$this->goods_id = $group_buy['goods_id'];
		
		$goods = goods_info($this->goods_id, $this->region_id, $this->area_id, $this->area_city);


		$now = time()+8*3600;
		if($group_buy['end_date'] > $now && $group_buy['start_date'] < $now){
			$goods['is_end'] = 1;
		}else if($group_buy['start_date'] > $now){
			$goods['is_end'] = 0;
		}else{
			$goods['is_end'] = 2;
		}
		
		// var_dump($goods);
		if (empty($goods)) {
			ecs_header("Location: ./\n");
			exit();
		}

		$sql = 'SELECT count(*) FROM ' . $this->ecs->table('collect_store') . ' WHERE ru_id = ' . $goods['user_id'];
		$collect_number = $this->db->getOne($sql);
		$this->assign('collect_number', $collect_number ? $collect_number : 0);
		$mc_all = ments_count_all($this->goods_id);
		$mc_one = ments_count_rank_num($this->goods_id, 1);
		$mc_two = ments_count_rank_num($this->goods_id, 2);
		$mc_three = ments_count_rank_num($this->goods_id, 3);
		$mc_four = ments_count_rank_num($this->goods_id, 4);
		$mc_five = ments_count_rank_num($this->goods_id, 5);
		$comment_all = get_conments_stars($mc_all, $mc_one, $mc_two, $mc_three, $mc_four, $mc_five);

		if (0 < $goods['user_id']) {
			$merchants_goods_comment = get_merchants_goods_comment($goods['user_id']);
			$this->assign('merch_cmt', $merchants_goods_comment);
		}

		$this->assign('comment_all', $comment_all);
		$sql = 'select a.kf_im_switch, b.is_IM,a.ru_id,a.province, a.city, a.kf_type, a.kf_ww, a.kf_qq, a.meiqia, a.shop_name, a.kf_appkey,kf_secretkey from {pre}seller_shopinfo as a left join {pre}merchants_shop_information as b on a.ru_id=b.user_id where a.ru_id=\'' . $goods['user_id'] . '\' ';
		
		$basic_info = $this->db->getRow($sql);
		
		$info_ww = $basic_info['kf_ww'] ? explode("\r\n", $basic_info['kf_ww']) : '';
		$info_qq = $basic_info['kf_qq'] ? explode("\r\n", $basic_info['kf_qq']) : '';
		$kf_ww = $info_ww ? $info_ww[0] : '';
		$kf_qq = $info_qq ? $info_qq[0] : '';
		$basic_ww = $kf_ww ? explode('|', $kf_ww) : '';
		$basic_qq = $kf_qq ? explode('|', $kf_qq) : '';
		$basic_info['kf_ww'] = $basic_ww ? $basic_ww[1] : '';
		$basic_info['kf_qq'] = $basic_qq ? $basic_qq[1] : '';
		$customer_service = dao('shop_config')->where(array('code' => 'customer_service'))->getField('value');
		$zkf = dao('seller_shopinfo')->field('kf_type, kf_qq, kf_ww')->where(array('ru_id' => '0'))->find();
		$this->assign('customer_service', $customer_service);

		if ($customer_service == 0) {
			$basic_info['kf_ww'] = preg_replace('/^[^\\-]*\\|/is', '', $zkf['kf_ww']);
			$basic_info['kf_qq'] = preg_replace('/^[^\\-]*\\|/is', '', $zkf['kf_qq']);
		}

		if ($goods['user_id'] == 0) {
			if ($this->db->getOne('SELECT kf_im_switch FROM {pre}seller_shopinfo WHERE ru_id = 0')) {
				$basic_info['is_dsc'] = true;
				$im_dialog = M()->query('SHOW TABLES LIKE "{pre}im_dialog"');

				if ($im_dialog) {
					$this->assign('kefu', 1);
				}
			}
			else {
				$basic_info['is_dsc'] = false;
			}
		}
		else {
			$basic_info['is_dsc'] = false;
		}

		$this->assign('basic_info', $basic_info);
		$good_comment = get_good_comment($this->goods_id, 4, 1, 0, 1);
		$this->assign('good_comment', $good_comment);
		$this->assign('goods_id', $this->goods_id);
		$new_goods = get_recommend_goods('new', '', $this->region_id, $this->area_info['region_id'], $this->area_city, $goods['user_id']);
		$this->assign('new_goods', $new_goods);
		$this->assign('type', 0);
		$goods['url'] = build_uri('goods', array('gid' => $this->goods_id), $goods['goods_name']);

		
		// var_dump($goods['goods_desc']);
		$sql = "select * from dsc_paipai_list where ppj_id = {$_GET['id']}";
		$re = $GLOBALS['db']->getRow($sql);

		$this->assign('re', $re);

        //查询订单表保证金支付
		$sqla = "select order_id,order_sn,pay_status from dsc_order_info where user_id = {$_SESSION['user_id']} and ppj_id={$re['ppj_id']} and ppj_no ={$re['ppj_no']} and pay_status = '10' and extension_code = 'paipai_buy' ORDER BY order_id DESC LIMIT 1";

		$rea = $GLOBALS['db']->getRow($sqla);
		if($rea){
           //查询保证金详情
           $spmsql="SELECT * FROM {pre}paipai_seller_pay_margin WHERE order_id={$rea['order_id']} ";
           $spm_data = $GLOBALS['db']->getRow($spmsql);
		   if($spm_data){
                //查询是否有交易成功一笔
                $ok_sql="SELECT * FROM {pre}paipai_seller_ok WHERE spm_id={$spm_data['spm_id']}";
                $ok_data = $GLOBALS['db']->getRow($ok_sql);
                if($ok_data){
                     $goods['ok_status'] = $ok_data['status'];
                }else{
                	 $goods['ok_status'] = 5;   //没有订单
                }
		   }
		}		


		$goods['pay_status'] = $rea['pay_status'];

		$this->assign('goods', $goods);
        


		$sql = 'SELECT * FROM {pre}goods_gallery WHERE goods_id = ' . $this->goods_id;
		$goods_img = $this->db->query($sql);

		foreach ($goods_img as $key => $val) {
			$goods_img[$key]['img_url'] = get_image_path($val['img_url']);
		}

		$this->assign('goods_img', $goods_img);

		if ($_SESSION['user_id']) {
			$where['user_id'] = $_SESSION['user_id'];
			$where['goods_id'] = $this->goods_id;
			$rs = $this->db->table('collect_goods')->where($where)->count();

			if (0 < $rs) {
				$this->assign('goods_collect', 1);
			}
		}

		$warehouse_list = get_warehouse_list_goods();
		$this->assign('warehouse_list', $warehouse_list);
		$this->assign('area_id', $this->area_info['region_id']);
		$this->assign('warehouse_id', $this->region_id);
		$this->assign('region_id', $this->region_id);
		$properties = get_goods_properties($this->goods_id, $this->region_id, $this->area_id);
		$this->assign('properties', $properties['pro']);
		$default_spe = '';

		if ($properties['spe']) {
			foreach ($properties['spe'] as $k => $v) {
				if ($v['attr_type'] == 1) {
					if (0 < $v['is_checked']) {
						foreach ($v['values'] as $key => $val) {
							$default_spe .= $val['checked'] ? $val['label'] . '、' : '';
						}
					}
					else {
						foreach ($v['values'] as $key => $val) {
							if ($key == 0) {
								$default_spe .= $val['label'] . '、';
							}
						}
					}
				}
			}
		}

		$this->assign('specification', $properties['spe']);
		$position = assign_ur_here(0, $goods['goods_name']);
		$seo = get_seo_words('group_content');

		foreach ($seo as $key => $value) {
			$seo[$key] = html_in(str_replace(array('{name}', '{description}'), array($group_buy['act_name'], $group_buy['act_desc']), $value));
		}

		$page_title = !empty($seo['title']) ? $seo['title'] : $position['title'];
		$keywords = !empty($seo['keywords']) ? $seo['keywords'] : (!empty($goods['keywords']) ? $goods['keywords'] : C('shop.shop_keywords'));
		$description = !empty($seo['description']) ? $seo['description'] : (!empty($goods['goods_brief']) ? $goods['goods_brief'] : C('shop.shop_desc'));
		$share_data = array('title' => $page_title, 'desc' => $description, 'link' => '', 'img' => $goods['goods_img']);
		$this->assign('share_data', $this->get_wechat_share_content($share_data));
		$this->assign('page_title', $page_title);
		$this->assign('keywords', $keywords);
		$this->assign('description', $description);

		$sql = 'SELECT ld.goods_desc FROM {pre}link_desc_goodsid AS dg, {pre}link_goods_desc AS ld WHERE dg.goods_id = ' . $this->goods_id . '  AND dg.d_id = ld.id AND ld.review_status > 2';

		$link_desc = $this->db->getOne($sql);
		

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

		if (empty($goods['desc_mobile']) && empty($goods['goods_desc'])) {
			$sql = 'SELECT ld.goods_desc FROM {pre}link_desc_goodsid AS dg, {pre}link_goods_desc AS ld WHERE dg.goods_id = ' . $this->goods_id . '  AND dg.d_id = ld.id AND ld.review_status > 2';
			$goods_desc = $this->db->getOne($sql);
		}

		if (!empty($goods_desc)) {
			$goods_desc = preg_replace('/<img(.*?)src=/i', '<img${1}class="lazy" src=', $goods_desc);
		}
	

		if (empty($goods['desc_mobile']) && empty($goods['goods_desc'])) {
			$goods_desc = $link_desc;
		}

		

		if (!empty($goods_desc)) {
			$goods_desc = preg_replace('/height\\="[0-9]+?"/', '', $goods_desc);
			$goods_desc = preg_replace('/width\\="[0-9]+?"/', '', $goods_desc);
			$goods_desc = preg_replace('/style=.+?[*|"]/i', '', $goods_desc);
		}

        //当前期保证金支付人数

		if($_SESSION['user_id'] != ''){
			//用户出价记录
			$fsql="SELECT gbu.bid_price FROM dsc_paipai_seller_pay_margin AS spm LEFT JOIN dsc_paipai_goods_bid_user AS gbu ON spm.spm_id = gbu.spm_id  WHERE spm.ppj_id={$re['ppj_id']} AND spm.ppj_no ={$re['ppj_no']} AND spm.user_id={$_SESSION['user_id']}";
			$user_bid_one = $GLOBALS['db']->getRow($fsql);
			$this->assign('user_bid_one', $user_bid_one);
			
			//订单id
			$uo_sql="SELECT order_id FROM dsc_order_info WHERE ppj_id={$re['ppj_id']} AND ppj_no ={$re['ppj_no']} AND user_id={$_SESSION['user_id']} ORDER BY order_id DESC LIMIT 1 ";
			$user_order = $GLOBALS['db']->getRow($uo_sql);
			$this->assign('user_order', $user_order);
		}
		
		//统计保证金支付人数

		$pmsql = "SELECT count(user_id) as count FROM dsc_paipai_seller_pay_margin WHERE ppj_id={$re['ppj_id']} AND ppj_no ={$re['ppj_no']}";
		$pm_data = $GLOBALS['db']->getRow($pmsql);
		$this->assign('pm_data', $pm_data);
		
        
        //循环播放出价金额
        $sqlu = "SELECT bu.bid_price,u.user_name FROM dsc_paipai_goods_bid_user AS bu LEFT JOIN dsc_users AS u ON bu.user_id=u.user_id  where bu.ppj_id={$re['ppj_id']} and bu.ppj_no ={$re['ppj_no']} and bu.is_status= '2' ";
		$price_list = $GLOBALS['db']->getAll($sqlu);
		$this->assign('price_list', $price_list);
		
		//查询当前用户是否购买一次以及第一次购买
		$musql="SELECT so.* FROM dsc_paipai_seller_ok AS so LEFT JOIN dsc_paipai_seller_pay_margin spm ON so.spm_id=spm.spm_id WHERE so.buy_id = {$_SESSION['user_id']} and so.ppj_id={$re['ppj_id']} and so.ppj_no ={$re['ppj_no']} AND spm.ls_pay_ok=1 ";
		$order_one=$GLOBALS['db']->getRow($musql);
		$this->assign('order_one', $order_one);

		$this->assign('goods_desc', $goods_desc);
		$this->display();
	}




/////////


	public function actionPrice()
	{
		$res = array('err_msg' => '', 'err_no' => 0, 'result' => '', 'qty' => 1);
		$attr = I('attr');
		$number = I('number', 1, 'intval');
		$this->goods_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$attr_id = !empty($attr) ? explode(',', $attr) : array();
		$warehouse_id = I('request.warehouse_id', 0, 'intval');
		$this->area_id = I('request.area_id', 0, 'intval');
		$onload = I('request.onload', '', 'trim');
		$goods = get_goods_info($this->goods_id, $warehouse_id, $this->area_id, $this->area_city);

		if ($this->goods_id == 0) {
			$res['err_msg'] = L('err_change_attr');
			$res['err_no'] = 1;
		}
		else {
			if ($number == 0) {
				$res['qty'] = $number = 1;
			}
			else {
				$res['qty'] = $number;
			}

			$products = get_warehouse_id_attr_number($this->goods_id, $_REQUEST['attr'], $goods['user_id'], $warehouse_id, $this->area_id, $area_city);
			$attr_number = $products['product_number'];

			if (0 < $goods['cloud_id']) {
				$sql = 'SELECT product_number,cloud_product_id FROM' . $GLOBALS['ecs']->table('products') . 'WHERE product_id = \'' . $products['product_id'] . '\'';
				$product = $GLOBALS['db']->getRow($sql);
				$attr_number = get_jigon_products_stock($product);
			}
			else if ($goods['model_attr'] == 1) {
				$table_products = 'products_warehouse';
				$type_files = ' AND warehouse_id = \'' . $warehouse_id . '\'';
			}
			else if ($goods['model_attr'] == 2) {
				$table_products = 'products_area';
				$type_files = ' AND area_id = \'' . $this->area_id . '\'';

				if ($GLOBALS['_CFG']['area_pricetype'] == 1) {
					$type_files .= ' AND city_id = \'' . $area_city . '\' ';
				}
			}
			else {
				$table_products = 'products';
				$type_files = '';
			}

			$sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table($table_products) . (' WHERE goods_id = \'' . $this->goods_id . '\'') . $type_files . ' LIMIT 0, 1';
			$prod = $GLOBALS['db']->getRow($sql);

			if (empty($prod)) {
				$attr_number = $goods['goods_number'];
			}

			$attr_number = !empty($attr_number) ? $attr_number : 0;
			$res['attr_number'] = $attr_number;
		}

		exit(json_encode($res));
	}

/*下单购买
 * 
 * 
 */
	public function actionBuy()
	{
		$this->check_login();
		
		$warehouse_id = I('request.warehouse_id', 0, 'intval');
		
		$this->area_id = isset($_REQUEST['area_id']) ? intval($_REQUEST['area_id']) : 0;
		
		$this->groupbuyid = I('request.group_buy_id', 0, 'intval');   // 活动ID

		if ($this->groupbuyid <= 0) {
			ecs_header("Location: ./\n");
			exit();
		}

         $sellers_fee =number_format(($_POST['sellers_fee']),2);
		 $sellers_fee=str_replace(array(","),"",$sellers_fee);
//         echo $sellers_fee;
//         exit();
         
         //购买数量 
		$number = isset($_POST['number']) ? intval($_POST['number']) : 1;		
		$number = $number < 1 ? 1 : $number;
		
//获取该活动的详情信息			
		$group_buy = paipai_buy_info($this->groupbuyid, $number);
		
		if (empty($group_buy)) {
			ecs_header("Location: ./\n");
			exit();
		}

 // 判断活动是否中进行中
		if ($group_buy['status'] != GBS_UNDER_WAY) {
			show_message(L('gb_error_status'), '', '', 'error');
		}
 // 获取 商品详情
		$goods = goods_info($group_buy['goods_id'], $this->region_id, $this->area_id, $this->area_city);
		if (empty($goods)) {
			ecs_header("Location: ./\n");
			exit();
		}
//获取拍拍活动的开始时间和结束时间
		$start_date = $group_buy['xiangou_start_date'];		
		$end_date = $group_buy['xiangou_end_date'];

//调用 app/Helpers/common_helper.php  2134行,该商品已售数量
		$order_goods = get_for_purchasing_goods($start_date, $end_date, $group_buy['goods_id'], $_SESSION['user_id'], 'paipai_buy');
		//商品包括本次的 总数量		
		$restrict_amount = $number + $order_goods['goods_number'];
	
		//判断购买的数量是否已超过限购的数量, $group_buy['restrict_amount']= 该活动的总库存
		if (0 < $group_buy['restrict_amount'] && $group_buy['restrict_amount'] < $restrict_amount) {
			show_message(L('gb_error_restrict_amount'), '', '', 'error'); //'对不起，您购买的商品数量已达到限购数量！';
		}
		else {
			if (0 < $group_buy['restrict_amount'] && $group_buy['restrict_amount'] - $order_goods['goods_number'] < $number) {
				show_message(L('gb_error_goods_lacking'), '', '', 'error'); //对不起，商品库存不足，请您修改数量！
			}
		}
		
      /// 商品属性
		$specs = isset($_POST['goods_spec']) ? htmlspecialchars(trim($_POST['goods_spec'])) : '';
		if ($specs) {
			$_specs = explode(',', $specs);
			$product_info = get_products_info($goods['goods_id'], $_specs, $warehouse_id, $this->area_id, $this->area_city);
		}

		empty($product_info) ? $product_info = array('product_number' => 0, 'product_id' => 0) : '';

		if ($goods['model_attr'] == 1) {
			$table_products = 'products_warehouse';
			$type_files = ' and warehouse_id = \'' . $warehouse_id . '\'';
		}
		else if ($goods['model_attr'] == 2) {
			$table_products = 'products_area';
			$type_files = ' and area_id = \'' . $this->area_id . '\'';

			if ($GLOBALS['_CFG']['area_pricetype'] == 1) {
				$type_files .= ' AND city_id = \'' . $area_city . '\'';
			}
		}
		else {
			$table_products = 'products';
			$type_files = '';
		}

		$sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table($table_products) . ' WHERE goods_id = \'' . $goods['goods_id'] . '\'' . $type_files . ' LIMIT 0, 1';
		$prod = $GLOBALS['db']->getRow($sql);

		if ($GLOBALS['_CFG']['use_storage'] == 1) {
			if ($prod && $product_info['product_number'] < $number) {
				show_message(L('gb_error_goods_lacking'), '', '', 'error');
			}
			else if ($goods['goods_number'] < $number) {
				show_message(L('gb_error_goods_lacking'), '', '', 'error');
			}
		}

		$attr_list = array();
		//good_attr  具体商品属性表
		$sql = 'SELECT a.attr_name, g.attr_value ' . 'FROM ' . $GLOBALS['ecs']->table('goods_attr') . ' AS g, ' . $GLOBALS['ecs']->table('attribute') . ' AS a ' . 'WHERE g.attr_id = a.attr_id ' . 'AND g.goods_attr_id ' . db_create_in($specs);
		$res = $GLOBALS['db']->query($sql);

		foreach ($res as $row) {
			$attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
		}
		$goods_attr = join(chr(13) . chr(10), $attr_list);
		
		clear_cart(CART_GROUP_BUY_GOODS);
		
		$area_info = get_area_info($this->province_id);
		
		
		$this->area_id = $area_info['region_id'];
		
		$where = 'regionId = \'' . $this->province_id . '\'';
		
		$date = array('parent_id');
		
		$this->region_id = get_table_date('region_warehouse', $where, $date, 2);

		if (!empty($_SESSION['user_id'])) {
			$sess = '';
		}
		else {
			$sess = real_cart_mac_ip();
		}

 // 商品价格
		//$goods_price = 0 < $group_buy['ppj_margin_fee'] ? $group_buy['ppj_margin_fee'] : $group_buy['cur_price'];
		$goods_price =$group_buy['ppj_margin_fee'];
		
		$cart = array('ppj_id' => $group_buy['ppj_id'],'ppj_no' => $group_buy['ppj_no'],'sellers_fee' => $sellers_fee,'user_id' => $_SESSION['user_id'], 'session_id' => $sess, 'goods_id' => $group_buy['goods_id'], 'product_id' => $product_info['product_id'], 'goods_sn' => addslashes($goods['goods_sn']), 'goods_name' => addslashes($goods['goods_name']), 'market_price' => $goods['market_price'], 'goods_price' => $goods_price, 'goods_number' => $number, 'goods_attr' => addslashes($goods_attr), 'goods_attr_id' => $specs, 'ru_id' => $goods['user_id'], 'warehouse_id' => $this->region_id, 'area_id' => $this->area_id, 'is_real' => $goods['is_real'], 'extension_code' => addslashes($goods['extension_code']), 'parent_id' => 0, 'rec_type' => CART_GROUP_BUY_GOODS, 'is_gift' => 0);
		
		$this->db->autoExecute($GLOBALS['ecs']->table('cart'), $cart, 'INSERT');
		
		$_SESSION['flow_type'] = CART_GROUP_BUY_GOODS;
		
		$_SESSION['extension_code'] = 'paipai_buy';
		
		$_SESSION['cart_value'] = '';
		
		$_SESSION['extension_id'] = $this->groupbuyid;
		
		$_SESSION['direct_shopping'] = 5;
		
		$_SESSION['browse_trace'] = 'paipai_buy';
		
		//
		$_SESSION['ppj_no'] =$group_buy['ppj_no'] ;
		
		$this->redirect('flow/index/indexpaipai', array('direct_shopping' => 5));
		
		exit();
	}



//////////////////////////////////////////////////////////////////////////////


	private function check_login()
	{
		if (!$_SESSION['user_id']) {
			$back_act = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : __HOST__ . $_SERVER['REQUEST_URI'];
			$this->redirect('user/login/index', array('back_act' => urlencode($back_act)));
		}
	}


/*
 * 初始
 * 
 */
	private function init_params()
	{
		if (!isset($_COOKIE['province'])) {
			$area_array = get_ip_area_name();

			if ($area_array['county_level'] == 2) {
				$date = array('region_id', 'parent_id', 'region_name');
				$where = 'region_name = \'' . $area_array['area_name'] . '\' AND region_type = 2';
				$city_info = get_table_date('region', $where, $date, 1);
				$date = array('region_id', 'region_name');
				$where = 'region_id = \'' . $city_info[0]['parent_id'] . '\'';
				$province_info = get_table_date('region', $where, $date);
				$where = 'parent_id = \'' . $city_info[0]['region_id'] . '\' order by region_id asc limit 0, 1';
				$district_info = get_table_date('region', $where, $date, 1);
			}
			else if ($area_array['county_level'] == 1) {
				$area_name = $area_array['area_name'];
				$date = array('region_id', 'region_name');
				$where = 'region_name = \'' . $area_name . '\'';
				$province_info = get_table_date('region', $where, $date);
				$where = 'parent_id = \'' . $province_info['region_id'] . '\' order by region_id asc limit 0, 1';
				$city_info = get_table_date('region', $where, $date, 1);
				$where = 'parent_id = \'' . $city_info[0]['region_id'] . '\' order by region_id asc limit 0, 1';
				$district_info = get_table_date('region', $where, $date, 1);
			}
		}

		$order_area = get_user_order_area($this->user_id);
		
		$user_area = get_user_area_reg($this->user_id);
		
		if ($order_area['province'] && 0 < $this->user_id) {
			$this->province_id = $order_area['province'];
			$this->city_id = $order_area['city'];
			$this->district_id = $order_area['district'];
		}
		else {
			if (0 < $user_area['province']) {
				$this->province_id = $user_area['province'];
				cookie('province', $user_area['province']);
				$this->region_id = get_province_id_warehouse($this->province_id);
			}
			else {
				$sql = 'select region_name from ' . $this->ecs->table('region_warehouse') . ' where regionId = \'' . $province_info['region_id'] . '\'';
				$warehouse_name = $this->db->getOne($sql);
				$this->province_id = $province_info['region_id'];
				$cangku_name = $warehouse_name;
				$this->region_id = get_warehouse_name_id(0, $cangku_name);
			}

			if (0 < $user_area['city']) {
				$this->city_id = $user_area['city'];
				cookie('city', $user_area['city']);
			}
			else {
				$this->city_id = $city_info[0]['region_id'];
			}

			if (0 < $user_area['district']) {
				$this->district_id = $user_area['district'];
				cookie('district', $user_area['district']);
			}
			else {
				$this->district_id = $district_info[0]['region_id'];
			}
		}

		$this->province_id = isset($_COOKIE['province']) ? $_COOKIE['province'] : $this->province_id;
		$child_num = get_region_child_num($this->province_id);

		if (0 < $child_num) {
			$this->city_id = isset($_COOKIE['city']) ? $_COOKIE['city'] : $this->city_id;
		}
		else {
			$this->city_id = '';
		}

		$child_num = get_region_child_num($this->city_id);

		if (0 < $child_num) {
			$this->district_id = isset($_COOKIE['district']) ? $_COOKIE['district'] : $this->district_id;
		}
		else {
			$this->district_id = '';
		}

		$this->region_id = !isset($_COOKIE['region_id']) ? $this->region_id : $_COOKIE['region_id'];
		$goods_warehouse = get_warehouse_goods_region($this->province_id);

		if ($goods_warehouse) {
			$this->regionId = $goods_warehouse['region_id'];
			if ($_COOKIE['region_id'] && $_COOKIE['regionid']) {
				$gw = 0;
			}
			else {
				$gw = 1;
			}
		}

		if ($gw) {
			$this->region_id = $this->regionId;
			cookie('area_region', $this->region_id);
		}

		cookie('goodsId', $this->goods_id);
		$sellerInfo = get_seller_info_area();

		if (empty($this->province_id)) {
			$this->province_id = $sellerInfo['province'];
			$this->city_id = $sellerInfo['city'];
			$this->district_id = 0;
			cookie('province', $this->province_id);
			cookie('city', $this->city_id);
			cookie('district', $this->district_id);
			$this->region_id = get_warehouse_goods_region($this->province_id);
		}

		$other = array('province_id' => $this->province_id, 'city_id' => $this->city_id);
		
		$warehouse_area_info = get_warehouse_area_info($other);
		
		$this->area_city = $warehouse_area_info['city_id'];
		
		cookie('area_city ', $this->area_city);
		
		
		
		$this->area_info = get_area_info($this->province_id);
	}
	
}

?>
