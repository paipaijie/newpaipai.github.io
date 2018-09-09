<?php
//zend by 多点乐  禁止倒卖 一经发现停止任何服务

///////////////////////////////////////////////////////////////////////////////////////////////////////
// $filter  过滤器，

function group_buy_list($ru_id)
{
	
	$result = get_filter();  //admin\includes\lib_main.php文件的第619行,取得上次的过滤条件

	if ($result === false) { // 
			
			//echo "kkk" ;
			
		$filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);		
		if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {			
			$filter['keyword'] = json_str_iconv($filter['keyword']);			
		}

		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'ga.ppj_id' : trim($_REQUEST['sort_by']);
		
		$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		
		$filter['review_status'] = empty($_REQUEST['review_status']) ? 0 : intval($_REQUEST['review_status']);
		
		$filter['seller_list'] = isset($_REQUEST['seller_list']) && !empty($_REQUEST['seller_list']) ? 1 : 0;
		
		$filter['rs_id'] = empty($_REQUEST['rs_id']) ? 0 : intval($_REQUEST['rs_id']);
		
		$adminru = get_admin_ru_id();

		if (0 < $adminru['rs_id']) {
			$filter['rs_id'] = $adminru['rs_id'];
		}

		$where = !empty($filter['keyword']) ? ' AND (ga.goods_name LIKE \'%' . mysql_like_quote($filter['keyword']) . '%\')' : '';

		if (0 < $ru_id) {
			$where .= ' and ga.user_id = \'' . $ru_id . '\'';
		}

		if ($filter['review_status']) {
			$where .= ' AND ga.review_status = \'' . $filter['review_status'] . '\' ';
		}

		$where .= get_rs_null_where('ga.user_id', $filter['rs_id']);
		
		$filter['store_search'] = !isset($_REQUEST['store_search']) ? -1 : intval($_REQUEST['store_search']);
		$filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
		$filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';
		
		$store_where = '';
		$store_search_where = '';

		if (-1 < $filter['store_search']) {
			if ($ru_id == 0) {
				if (0 < $filter['store_search']) {
					if ($_REQUEST['store_type']) {
						$store_search_where = 'AND msi.shopNameSuffix = \'' . $_REQUEST['store_type'] . '\'';
					}

					if ($filter['store_search'] == 1) {
						$where .= ' AND ga.user_id = \'' . $filter['merchant_id'] . '\' ';
					}
					else if ($filter['store_search'] == 2) {
						$store_where .= ' AND msi.rz_shopName LIKE \'%' . mysql_like_quote($filter['store_keyword']) . '%\'';
					}
					else if ($filter['store_search'] == 3) {
						$store_where .= ' AND msi.shoprz_brandName LIKE \'%' . mysql_like_quote($filter['store_keyword']) . '%\' ' . $store_search_where;
					}

					if (1 < $filter['store_search']) {
						$where .= ' AND (SELECT msi.user_id FROM ' . $GLOBALS['ecs']->table('merchants_shop_information') . ' as msi ' . (' WHERE msi.user_id = ga.user_id ' . $store_where . ') > 0 ');
					}
				}
				else {
					$where .= '  ga.user_id = 0';
				}
			}
		}

		$where .= !empty($filter['seller_list']) ? '  ga.user_id > 0 ' : '  ga.user_id = 0 ';
		
		
		$sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('paipai_list') . ' AS ga ' . ' WHERE ' . $where;
		
		$filter['record_count'] = $GLOBALS['db']->getOne($sql);
		
		
		$filter = page_and_size($filter);
		
		$sql = 'SELECT ga.* ' . 'FROM ' . $GLOBALS['ecs']->table('paipai_list') . ' AS ga ' . ' WHERE'   . $where  . (' ORDER BY ' . $filter['sort_by'] . ' ' . $filter['sort_order'] . ' ') . ' LIMIT ' . $filter['start'] . (', ' . $filter['page_size']);
		
		$filter['keyword'] = stripslashes($filter['keyword']);
		
		set_filter($filter, $sql);
		
	}
	else {  
		
		$sql = $result['sql'];
		
		$filter = $result['filter'];
		
	}
	$res = $GLOBALS['db']->query($sql);
	$list = array();
	while ($row = $GLOBALS['db']->fetchRow($res)) {
		$ext_info = unserialize($row['ext_info']);			
		$stat = group_buy_stat($row['ppj_id'],  $ext_info['ppj_margin_fee']);	  // 	
		$arr = array_merge($row, $stat, $ext_info);	
		$price_ladder = $arr['price_ladder'];
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
		$cur_price = $price_ladder[0]['price'];
		$cur_amount = $stat['valid_goods'];
		foreach ($price_ladder as $amount_price) {	
			if ($amount_price['amount'] <= $cur_amount) {
				$cur_price = $amount_price['price'];
			}
			else {
				break;
			}
		}		
		$arr['cur_price'] = $cur_price;
		$status = group_buy_status($arr); //  group_buy_status 在 lib_goos.php中定义
		$arr['start_time'] = local_date($GLOBALS['_CFG']['date_format'], $arr['start_time']);
		$arr['end_time'] = local_date($GLOBALS['_CFG']['date_format'], $arr['end_time']);
		$arr['cur_status'] = $GLOBALS['_LANG']['gbs'][$status];
		$arr['user_name'] = get_shop_name($arr['user_id'], 1);
		$list[] = $arr;
	}
	$arr = array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	return $arr;
}


/////////////////////////////获取活动详情信息//////////////////////////////////////////////////////////////////////////

function goods_group_buy($goods_id)
{
	$sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('goods_activity') . (' WHERE goods_id = \'' . $goods_id . '\' ') . ' AND act_type = \'' . GAT_GROUP_BUY . '\'' . ' AND start_time <= ' . gmtime() . ' AND end_time >= ' . gmtime();
	
	return $GLOBALS['db']->getRow($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////获取活动详情信息//////////////////////////////////////////////////////////////////////////

function goods_paipai_buy($goods_id)
{
	$paipai_status=2;
	$sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('paipai_list') . (' WHERE goods_id = \'' . $goods_id . '\' ') . ' AND ppj_staus <> \'' . $paipai_status . '\''. ' AND start_time <= ' . gmtime() . ' AND end_time >= ' . gmtime();
	return $GLOBALS['db']->getRow($sql);
}
//获取最近一条拍拍活动商品信息
function goods_paipai_activity($goods_id){

	$sql="SELECT *  FROM `dsc_paipai_list`  WHERE goods_id='{$goods_id}'  ORDER BY ppj_no DESC LIMIT 1 ";
    return $GLOBALS['db']->fetchRow($GLOBALS['db']->query($sql));
}
//查询产品的基本信息
function goods_news($goods_id){
    $sql="SELECT goods_id,goods_sn,cost_price FROM dsc_goods WHERE goods_id='{$goods_id}' ";
    return $GLOBALS['db']->fetchRow($GLOBALS['db']->query($sql));
}

///////////////////////////////////////////////////////////////////////////////////////////////////////

function list_link($is_add = true)
{
	$href = 'group_buy.php?act=list';

	if (!$is_add) {
		$href .= '&' . list_link_postfix();
	}

	return array('href' => $href, 'text' => $GLOBALS['_LANG']['group_buy_list']);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////

define('IN_ECS', true);
require dirname(__FILE__) . '/includes/init.php';
require_once ROOT_PATH . 'includes/lib_goods.php';
require_once ROOT_PATH . 'includes/lib_order.php';

admin_priv('group_by');
$adminru = get_admin_ru_id();

if ($adminru['ru_id'] == 0) {
	$smarty->assign('priv_ru', 1);
}
else {
	$smarty->assign('priv_ru', 0);
}


//设置默认访问
if (empty($_REQUEST['act'])) {
	$_REQUEST['act'] = 'list';
}
else {
	$_REQUEST['act'] = trim($_REQUEST['act']);
}





//判断哪个控制器

if ($_REQUEST['act'] == 'list') {     //list 首页
	

	$smarty->assign('full_page', 1);
	
	$smarty->assign('ur_here', $_LANG['paipai_buy_list']);
	
	$smarty->assign('action_link', array('href' => 'paipai_buy.php?act=add', 'text' => $_LANG['add_group_buy']));
	
	$list = group_buy_list($adminru['ru_id']);
	
	$smarty->assign('group_buy_list', $list['item']);
	
	$smarty->assign('filter', $list['filter']);
	
	$smarty->assign('record_count', $list['record_count']); // 记录条数
	
	$smarty->assign('page_count', $list['page_count']);
	
	$store_list = get_common_store_list();
	
	$smarty->assign('store_list', $store_list);
	
	$sort_flag = sort_flag($list['filter']);
	
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	
	self_seller(BASENAME($_SERVER['PHP_SELF']));
	
	assign_query_info();
	
	
	$smarty->display('paipai_buy_list.dwt');
	
	
	
	
	
}
else if ($_REQUEST['act'] == 'query') {     //查询功能
	$list = group_buy_list($adminru['ru_id']);
	$smarty->assign('group_buy_list', $list['item']);
	$smarty->assign('filter', $list['filter']);
	$smarty->assign('record_count', $list['record_count']);
	$smarty->assign('page_count', $list['page_count']);
	$store_list = get_common_store_list();
	$smarty->assign('store_list', $store_list);
	$sort_flag = sort_flag($list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	
	make_json_result($smarty->fetch('paipai_buy_list.dwt'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
	
}
else {
	if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') {  // 添加和修改活动
		
		if ($_REQUEST['act'] == 'add') { //添加功能
			
			$group_buy = array(
			
				'act_id'       => 0,
				
				'start_time'   => date('Y-m-d H:i:s', time() + 86400),
				
				'end_time'     => date('Y-m-d H:i:s', time() + 4 * 86400),
				
				'price_ladder' => array(
				
					array('amount' => 0, 'price' => 0)
					
					)
				);
		}
		
		else {    // 修改功能
			//  测试提交功能
			$group_buy_id = intval($_REQUEST['id']);

			if ($group_buy_id <= 0) {
				exit('invalid param');
			}
			
			//$group_buy = group_buy_info($group_buy_id,0,'seller');			
			$sql = "SELECT * FROM dsc_paipai_list where ppj_id = '$group_buy_id'";
			$result = $db->query($sql);
			$shuju1 = $result->fetch_object();
			//转换为数组
			$group_buy = get_object_vars($shuju1); 
			//过滤
			$ext_info = unserialize($group_buy['ext_info']);
			//合并
			$group_buy = array_merge($group_buy, $ext_info);	

			$group_buy['formated_start_date'] = local_date('Y-m-d H:i:s', $group_buy['start_time']);
			$group_buy['formated_end_date'] = local_date('Y-m-d H:i:s', $group_buy['end_time']);
		
			//价格遍历
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
			$group_buy['start_time'] = $group_buy['formated_start_date'];
			$group_buy['end_time'] = $group_buy['formated_end_date'];
			
		}
		$smarty->assign('group_buy', $group_buy);	
		$smarty->assign('ur_here', $_LANG['add_group_buy']);		
		$smarty->assign('action_link', list_link($_REQUEST['act'] == 'add'));		
		$smarty->assign('ru_id', $adminru['ru_id']);
		if ($_REQUEST['act'] == 'edit') {
			$smarty->assign('form_action', 'update');
		}
		else {
			$smarty->assign('form_action', 'insert');
		}
		set_default_filter();		
		assign_query_info();		
		$smarty->display('paipai_buy_info.dwt');	
	}
	
	// 添加和编辑拍拍活动
	else if ($_REQUEST['act'] == 'insert_update') {
		$group_buy_id = intval($_POST['act_id']);
		if (isset($_POST['finish']) || isset($_POST['succeed']) || isset($_POST['fail']) || isset($_POST['mail'])) {
			if ($group_buy_id <= 0) {
				sys_msg($_LANG['error_group_buy'], 1); //'您要操作的拍拍活动不存在'
			}
			//$group_buy = group_buy_list($group_buy_id, 0, 'seller');
			//if (empty($group_buy)) {
			//	sys_msg($_LANG['error_group_buy'], 1); //您要操作的拍拍活动不存在
			//}
		}
		if (isset($_POST['finish'])) {
			//正在进行中的活动可以点击结束
			/*
			if ($group_buy['ppj_staus'] != GBS_UNDER_WAY) {  // 已经开始的活动
				sys_msg($_LANG['error_status'], 1); //当前状态不能执行该操作
			}
			*/
			
			//var_dump($group_buy_id);
			//exit;
			
			$sql = "select * from dsc_paipai_list where ppj_id = '$group_buy_id'";
			$group_buy = $db->getRow($sql);
			
			
			if($group_buy['ppj_staus'] == 2 || $group_buy['ppj_staus'] == 0){
				sys_msg($_LANG['error_status'], 1);
			}
			
			$sql = 'UPDATE ' . $ecs->table('paipai_list') . ' SET end_time = \'' . gmtime() . '\' ,ppj_staus = 2 ' . ('WHERE ppj_id = \'' . $group_buy_id . '\' LIMIT 1');
			
			$db->query($sql);
			
			clear_cache_files();
			$links = array(
				array('href' => 'paipai_buy.php?act=list', 'text' => $_LANG['back_list']) //返回拍拍活动列表
				);
			sys_msg($_LANG['edit_success'], 0, $links);  //编辑团购活动成功
			
		}
		else if (isset($_POST['succeed'])) {
			$sql = "select * from dsc_paipai_list where ppj_id = '$group_buy_id'";
			$group_buy = $db->getRow($sql);
			if ($group_buy['ppj_staus'] != 2) {  //结束未处理
				sys_msg($_LANG['error_status'], 1); //当前状态不能执行该操作
			}
			exit;
			/*
			if (0 < $group_buy['total_order']) {
				
				$sql = 'SELECT order_id ' . 'FROM ' . $ecs->table('order_info') . ' WHERE extension_code = \'group_buy\' ' . ('AND extension_id = \'' . $group_buy_id . '\' ') . 'AND (order_status = \'' . OS_CONFIRMED . '\' or order_status = \'' . OS_UNCONFIRMED . '\')';
				
				$order_id_list = $db->getCol($sql);
				
				$final_price = $group_buy['trans_price'];
				
				$sql = 'UPDATE ' . $ecs->table('order_goods') . (' SET goods_price = \'' . $final_price . '\' ') . 'WHERE order_id ' . db_create_in($order_id_list);
				$db->query($sql);
				
				$sql = 'SELECT order_id, SUM(goods_number * goods_price) AS goods_amount ' . 'FROM ' . $ecs->table('order_goods') . ' WHERE order_id ' . db_create_in($order_id_list) . ' GROUP BY order_id';
				$res = $db->query($sql);
				

				while ($row = $db->fetchRow($res)) {
					$order_id = $row['order_id'];
					$goods_amount = floatval($row['goods_amount']);
					$order = order_info($order_id);

					if ($group_buy['deposit'] <= $order['surplus'] + $order['money_paid']) {
						$order['goods_amount'] = $goods_amount;

						if (0 < $order['insure_fee']) {
							$shipping = shipping_info($order['shipping_id']);
							$order['insure_fee'] = shipping_insure_fee($shipping['shipping_code'], $goods_amount, $shipping['insure']);
						}

						$order['order_amount'] = $order['goods_amount'] + $order['shipping_fee'] + $order['insure_fee'] + $order['pack_fee'] + $order['card_fee'] - $order['money_paid'] - $order['surplus'];
						

						if (0 < $order['order_amount']) {
							$order['pay_fee'] = pay_fee($order['pay_id'], $order['order_amount']);
						}
						else {
							$order['pay_fee'] = 0;
						}

						$order['order_amount'] += $order['pay_fee'];

						if (0 < $order['order_amount']) {
							$order['pay_status'] = PS_UNPAYED;
							$order['pay_time'] = 0;
						}
						else {
							$order['pay_status'] = PS_PAYED;
							$order['pay_time'] = gmtime();
						}

						if ($order['order_amount'] < 0) {
						}

						$order['order_status'] = OS_CONFIRMED;
						$order['confirm_time'] = gmtime();
						$order['add_time'] = gmtime();
						$order = addslashes_deep($order);
						update_order($order_id, $order);
					}
					else {
						$order['order_status'] = OS_CANCELED;
						$order['to_buyer'] = $_LANG['cancel_order_reason'];
						$order['pay_status'] = PS_UNPAYED;
						$order['pay_time'] = 0;
						$money = $order['surplus'] + $order['money_paid'];

						if (0 < $money) {
							$order['surplus'] = 0;
							$order['money_paid'] = 0;
							$order['order_amount'] = $money;
							order_refund($order, 1, $_LANG['cancel_order_reason'] . ':' . $order['order_sn']);
						}

						$order = addslashes_deep($order);
						update_order($order['order_id'], $order);
					}
				}
			}
			*/
			
			/*
			未开始为0
			进行中为1
			活动结束为2
			活动成功为3
			活动失败为4
			*/
			
			$sql = 'UPDATE ' . $ecs->table('paipai_list') . ' SET is_finished = \'' . GBS_SUCCEED . '\' ' . ('WHERE act_id = \'' . $group_buy_id . '\' LIMIT 1');
			$db->query($sql);
			
			clear_cache_files();
			
			
			$links = array(
				array('href' => 'paipai_buy.php?act=list', 'text' => $_LANG['back_list']) //返回拍拍活动列表
				);
			sys_msg($_LANG['edit_success'], 0, $links);
		}
		
		else if (isset($_POST['fail'])) {
			
			if ($group_buy['status'] != GBS_FINISHED) {
				sys_msg($_LANG['error_status'], 1);
			}

			if (0 < $group_buy['valid_order']) {
				$sql = 'SELECT * ' . 'FROM ' . $ecs->table('order_info') . ' WHERE extension_code = \'group_buy\' ' . ('AND extension_id = \'' . $group_buy_id . '\' ') . 'AND (order_status = \'' . OS_CONFIRMED . '\' OR order_status = \'' . OS_UNCONFIRMED . '\') ';
				$res = $db->query($sql);

				while ($order = $db->fetchRow($res)) {
					$order['order_status'] = OS_CANCELED;
					$order['to_buyer'] = $_LANG['cancel_order_reason'];
					$order['pay_status'] = PS_UNPAYED;
					$order['pay_time'] = 0;
					$money = $order['surplus'] + $order['money_paid'];

					if (0 < $money) {
						$order['surplus'] = 0;
						$order['money_paid'] = 0;
						$order['order_amount'] = $money;
						order_refund($order, 1, $_LANG['cancel_order_reason'] . ':' . $order['order_sn'], $money);
					}

					$order = addslashes_deep($order);
					update_order($order['order_id'], $order);
				}
			}

			$sql = 'UPDATE ' . $ecs->table('goods_activity') . ' SET is_finished = \'' . GBS_FAIL . '\', ' . ('act_desc = \'' . $_POST['act_desc'] . '\' ') . ('WHERE act_id = \'' . $group_buy_id . '\' LIMIT 1');
			$db->query($sql);
			clear_cache_files();
			$links = array(
				array('href' => 'group_buy.php?act=list', 'text' => $_LANG['back_list'])
				);
			sys_msg($_LANG['edit_success'], 0, $links);
		}
		
		else if (isset($_POST['mail'])) {
			if ($group_buy['status'] != GBS_SUCCEED) {
				sys_msg($_LANG['error_status'], 1);
			}

			$tpl = get_mail_template('group_buy');
			$count = 0;
			$send_count = 0;
			$sql = 'SELECT o.consignee, o.add_time, g.goods_number, o.order_sn, ' . 'o.order_amount, o.order_id, u.email ' . 'FROM ' . $ecs->table('order_info') . ' AS o, ' . $ecs->table('order_goods') . ' AS g, ' . $ecs->table('users') . ' AS u ' . 'WHERE o.order_id = g.order_id AND u.user_id = o.user_id ' . 'AND o.extension_code = \'group_buy\' ' . ('AND o.extension_id = \'' . $group_buy_id . '\' ') . 'AND o.order_status = \'' . OS_CONFIRMED . '\'';
			$res = $db->query($sql);

			while ($order = $db->fetchRow($res)) {
				$smarty->assign('consignee', $order['consignee']);
				$smarty->assign('add_time', local_date($_CFG['time_format'], $order['add_time']));
				$smarty->assign('goods_name', $group_buy['goods_name']);
				$smarty->assign('goods_number', $order['goods_number']);
				$smarty->assign('order_sn', $order['order_sn']);
				$smarty->assign('order_amount', price_format($order['order_amount']));
				$smarty->assign('shop_url', $ecs->url() . 'user.php?act=order_detail&order_id=' . $order['order_id']);
				$smarty->assign('shop_name', $_CFG['shop_name']);
				$smarty->assign('send_date', local_date($GLOBALS['_CFG']['time_format'], gmtime()));
				$content = $smarty->fetch('str:' . $tpl['template_content']);

				if (send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html'])) {
					$send_count++;
				}

				$count++;
			}

			sys_msg(sprintf($_LANG['mail_result'], $count, $send_count));
		}
		
     //////////////////////////////////////////////////////////////////////////		// 新增活动开始
		else {

			$goods_id = intval($_POST['goods_id']);  //获取变量的整数值

			if ($goods_id <= 0) {
				sys_msg($_LANG['error_goods_null']); //您没有选择拍拍商品
			}
			
			$goods_act=goods_paipai_activity($goods_id);  //查询该产品的最近一条的拍拍活动

			$info = goods_paipai_buy($goods_id);  //查询该产品的已有的拍拍活动

			if ($info && $info['ppj_id'] != $group_buy_id) {
				sys_msg($_LANG['error_goods_exist']);   //您选择的商品目前有一个拍拍活动正在进行
			}
			

			//该商品的拍拍期数，自动加1
			$ppj_no_new =intval($goods_act['ppj_no'])+1;

			$goods_name = $db->getOne('SELECT goods_name FROM ' . $ecs->table('goods') . (' WHERE goods_id = \'' . $goods_id . '\''));
			
			$ppj_name = empty($_POST['act_name']) ? $goods_name : sub_str($_POST['act_name'], 0, 255, false);

			//保证金     
			$deposit = floatval($_POST['ppl_margin_fee']);
			$goods_news=goods_news($goods_id);
			if ($deposit <= floatval($goods_news['cost_price'])) {    //保证金必须大于批发价 
				sys_msg($_LANG['notice_goods_deposit']);
			}
			
			// 起拍价
			$ppj_start_fee=floatval($_POST['ppj_start_fee']);
			if ($ppj_start_fee < 0) {
				$ppj_start_fee = 0;
			}

		    // 最低成交价
			$ppj_buy_fee=floatval($_POST['ppj_buy_fee']);
		    if ($ppj_buy_fee < 0) {				
				$ppj_buy_fee = 0;
			}
   
			//限购数量
			$restrict_amount = intval($_POST['restrict_amount']);
			if ($restrict_amount < 0) {
				$restrict_amount = 0;
			}

			//赠送积分
			$gift_integral = intval($_POST['gift_integral']);
			if ($gift_integral < 0) {
				$gift_integral = 0;
			}
			
			$ppj_startpay_time = intval($_POST['ppj_startpay_time']);
			
			$ppj_endtapy_time = intval($_POST['ppj_endtapy_time']);

            $ppj_addfee_type=2;   //1，按固定增长比例增加，2.自定义方式增加 ,此处默认阶梯，后期扩展
           
           //阶梯价格数组
			$price_ladder = array();
			
			$count = count($_POST['ladder_amount']);

			for ($i = $count - 1; 0 <= $i; $i--) {
				$amount = intval($_POST['ladder_amount'][$i]);

				if ($amount <= 0) {
					continue;
				}

				$price = round(floatval($_POST['ladder_price'][$i]), 2);

				if ($price <= 0) {
					continue;
				}

				$price_ladder[$amount] = array('amount' => $amount, 'price' => $price);
			}



			if ($deposit == 0) {
				
				if (!in_array(1, $_POST['ladder_amount'])) {
					$amount = 1;
					
					
					$price_ladder[$amount] = array('amount' => $amount, 'price' => $price);
				}
			}
			
			//商品的市场销售价
			$goods_price = $db->getOne('SELECT market_price FROM ' . $ecs->table('goods') . (' WHERE goods_id = \'' . $goods_id . '\''));
			$market_price=floatval($goods_price);
			if($deposit>$market_price){
				sys_msg($_LANG['notice_goods_deposit_market']);  //保证金不能大于商品销售价
			}

			if (count($price_ladder) < 1) {
				sys_msg($_LANG['error_price_ladder']);  //您没有输入有效的价格阶梯
			}


			$amount_list = array_keys($price_ladder);
			
			if (0 < $restrict_amount && $restrict_amount < max($amount_list)) {
				sys_msg($_LANG['error_restrict_amount']); //限购数量不能小于价格阶梯中的最大数量
			}

			ksort($price_ladder);
			$price_ladder = array_values($price_ladder);
			
			
			
			$start_time = local_strtotime($_POST['start_time']);//开始时间
			
			$end_time = local_strtotime($_POST['end_time']);//结束时间

			$ppj_createtime=gmtime();

			if ($end_time <= $start_time) {
				sys_msg($_LANG['invalid_time']);//输入的无效时间
			}
			
            $ppj_staus=0;     //活动状态   未开始
			 
			if($start_time < $ppj_createtime){
				$ppj_staus=1 ;//活动已开始,进行中
			}

			if($end_time < $ppj_createtime){
				$ppj_staus=2; //活动已结束
			}

			$is_hot = isset($_REQUEST['is_hot']) ? $_REQUEST['is_hot'] : 0;
			
			$is_new = isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
			$review_status=3;
			
			$group_buy = array('ppj_name' => $ppj_name,'act_desc' => $_POST['act_desc'],'goods_id' => $goods_id,'goods_name' => $goods_name,'start_time' => $start_time,'end_time' => $end_time, 'review_status' => $review_status, 'is_hot' => $is_hot, 'is_new' => $is_new, 'ppj_margin_fee' => $deposit, 'ppj_no' =>$ppj_no_new,'goods_count' => $restrict_amount,'ppj_start_fee' => $ppj_start_fee,'ppj_buy_fee' =>$ppj_buy_fee,'ppj_addfee_type' => $ppj_addfee_type,'ppj_startpay_time' => $ppj_startpay_time,'ppj_endpay_time' => $ppj_endpay_time ,'ppj_staus' => $ppj_staus,'ppj_createtime' => $ppj_createtime, 'ext_info' => serialize(array('price_ladder' => $price_ladder, 'restrict_amount' => $restrict_amount)),'gift_integral'=>$_POST['gift_integral']);
			
			clear_cache_files();

			/*
			*2018 8 21
			*fgc
			*/
			
			if (0 < $group_buy_id) {//
				
				//var_dump($group_buy_id);
				//exit;
				if (isset($_POST['review_status'])) {
					
					$review_status = !empty($_POST['review_status']) ? intval($_POST['review_status']) : 1;
					$review_content = !empty($_POST['review_content']) ? addslashes(trim($_POST['review_content'])) : '';
					$group_buy['review_status'] = $review_status;
					$group_buy['review_content'] = $review_content;
				}
				
				//保证金
				$deposit = floatval($_POST['ppl_margin_fee']);
				if ($deposit < 0) {
					$deposit = 0;
				}
				
				// 起拍价
				$ppj_start_fee=floatval($_POST['ppj_start_fee']);
				if ($ppj_start_fee < 0) {
					$ppj_start_fee = 0;
				}

				// 最低成交价
				$ppj_buy_fee=floatval($_POST['ppj_buy_fee']);
				if ($ppj_buy_fee < 0) {				
					$ppj_buy_fee = 0;
				}
	   
				//限购数量
				$restrict_amount = intval($_POST['restrict_amount']);
				if ($restrict_amount < 0) {
					$restrict_amount = 0;
				}

				//赠送积分
				$gift_integral = intval($_POST['gift_integral']);
				if ($gift_integral < 0) {
					$gift_integral = 0;
				}
				
				
				//判断状态，开始不能修改时间
				$sql = 'SELECT * FROM ' . $ecs->table('paipai_list') . (' WHERE ppj_id = \'' . $group_buy_id . '\' LIMIT 1');
				$result = $db->getRow($sql);
				if($ppj_staus = 0){
					$start_time = intval($_POST['start_time']);
					$end_time = intval($_POST['end_time']);
				}else{
					$start_time = $result['start_time'];
					$end_time = $result['end_time'];
				}
				
				$ppj_startpay_time = intval($_POST['ppj_startpay_time']);
					
				$ppj_endtapy_time = intval($_POST['ppj_endtapy_time']);

				$ppj_addfee_type=2;   //1，按固定增长比例增加，2.自定义方式增加 ,此处默认阶梯，后期扩展
			    

			    //阶梯价格数组
				$price_ladder = array();
				
				$count = count($_POST['ladder_amount']);

				for ($i = $count - 1; 0 <= $i; $i--) {
					$amount = intval($_POST['ladder_amount'][$i]);

					if ($amount <= 0) {
						continue;
					}

					$price = round(floatval($_POST['ladder_price'][$i]), 2);

					if ($price <= 0) {
						continue;
					}

					$price_ladder[$amount] = array('amount' => $amount, 'price' => $price);
				}
				
				$ext_info = serialize(array('price_ladder' => $price_ladder, 'restrict_amount' => $restrict_amount));
				
				
				$sql = "UPDATE ". $ecs->table("paipai_list") . ' SET ppj_margin_fee=\''.$deposit .'\',ppj_start_fee=\'' .$ppj_start_fee .'\',ppj_endpay_time=\''.$ppj_endpay_time.'\',ppj_startpay_time=\''.$ppj_startpay_time.'\',ppj_buy_fee=\''.$ppj_buy_fee.'\',goods_count=\''.$restrict_amount.'\',start_time=\''.$start_time.'\',end_time=\''.$end_time.'\',gift_integral=\''.$gift_integral.'\',ext_info=\''.$ext_info.'\' ' . (' WHERE ppj_id = \'' . $group_buy_id . '\' LIMIT 1');
				
				//var_dump($sql);
				//exit;
				
				$result = $db->query($sql);

				if($result < 0){
				// 	return true;
				// }else{
					return false;
				}
				
				//$db->autoExecute($ecs->table('paipai_list'), $group_buy, 'UPDATE', 'ppj_id = \'' . $group_buy_id . '\' AND _type = ' . GAT_PAIPAI_BUY);
				
				admin_log(addslashes($goods_name) . '[' . $group_buy_id . ']', 'edit', 'group_buy');
				
				$links = array(
					array('href' => 'paipai_buy.php?act=list&' . list_link_postfix(), 'text' => $_LANG['back_list'])
					);
				sys_msg($_LANG['edit_success'], 0, $links);
			}
			
			else {  // 新增

				if($group_buy['start_time'] < $goods_act['end_time']){
			           sys_msg($_LANG['error_goods_not_end']);   //您选择的商品目前有一个拍拍结束时间与添加的开始时间冲突
		        }

				$group_buy['user_id'] = $adminru['ru_id'];
				
				$db->autoExecute($ecs->table('paipai_list'), $group_buy, 'INSERT');
				
				admin_log(addslashes($goods_name), 'add', 'paipai_buy');
				
				$links = array(
					array('href' => 'paipai_buy.php?act=add', 'text' => $_LANG['continue_add']),
					array('href' => 'paipai_buy.php?act=list', 'text' => $_LANG['back_list'])
					);
				sys_msg($_LANG['add_success'], 0, $links);
			}
		}
		//////////////////////////////////////////////////////////////		// 新增活动结束
		
	}
	
	else if ($_REQUEST['act'] == 'batch') {
		check_authz_json('group_by');
		if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes'])) {
			sys_msg('没有选择任何数据', 1);
		}

		$ids = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;
		$del_count = count($_POST['checkboxes']);

		if (isset($_POST['type'])) {
			if ($_POST['type'] == 'batch_remove') {
				$group_buy = group_buy_info($id, 0, 'seller');

				if ($group_buy['valid_order'] <= 0) {
					$sql = 'DELETE FROM ' . $GLOBALS['ecs']->table('goods_activity') . ' WHERE act_id ' . db_create_in($ids);

					if ($db->query($sql)) {
						clear_cache_files();
						admin_log('', 'remove', 'group_buy');
						$links[] = array('text' => $_LANG['back_list'], 'href' => 'paipai_buy.php?act=list');
						sys_msg(sprintf($_LANG['batch_drop_success'], $del_count), 0, $links);
					}
				}
			}
			else if ($_POST['type'] == 'review_to') {
				$review_status = $_POST['review_status'];
				$review_content = !empty($_POST['review_content']) ? trim($_POST['review_content']) : '';
				$sql = 'UPDATE ' . $ecs->table('goods_activity') . (' SET review_status = \'' . $review_status . '\' ') . ' WHERE act_id ' . db_create_in($ids);

				if ($db->query($sql)) {
					$lnk[] = array('text' => $_LANG['back_list'], 'href' => 'paipai_buy.php?act=list&seller_list=1&' . list_link_postfix());
					sys_msg('团购审核状态设置成功', 0, $lnk);
				}
			}
		}
	}
	else if ($_REQUEST['act'] == 'group_goods') {
		check_authz_json('group_by');
		include_once ROOT_PATH . 'includes/cls_json.php';
		$json = new JSON();
		$filter = $json->decode($_GET['JSON']);
		$arr = get_goods_info($filter->goods_id);
		make_json_result($arr);
	}
	else if ($_REQUEST['act'] == 'search_goods') {
		check_authz_json('group_by');
		include_once ROOT_PATH . 'includes/cls_json.php';
		$json = new JSON();
		$filter = $json->decode($_GET['JSON']);
		$arr = get_goods_list($filter);
		make_json_result($arr);
	}
	else if ($_REQUEST['act'] == 'edit_deposit') {
		check_authz_json('group_by');
		$id = intval($_POST['id']);
		$val = floatval($_POST['val']);
		$sql = 'SELECT ext_info FROM ' . $ecs->table('goods_activity') . (' WHERE act_id = \'' . $id . '\' AND act_type = \'') . GAT_GROUP_BUY . '\'';
		$ext_info = unserialize($db->getOne($sql));
		$ext_info['deposit'] = $val;
		$sql = 'UPDATE ' . $ecs->table('goods_activity') . ' SET ext_info = \'' . serialize($ext_info) . '\'' . (' WHERE act_id = \'' . $id . '\'');
		$db->query($sql);
		clear_cache_files();
		make_json_result(number_format($val, 2));
	}
	else if ($_REQUEST['act'] == 'edit_restrict_amount') {
		
		check_authz_json('group_by');
		$id = intval($_POST['id']);
		$val = intval($_POST['val']);
		$sql = 'SELECT ext_info FROM ' . $ecs->table('goods_activity') . (' WHERE act_id = \'' . $id . '\' AND act_type = \'') . GAT_GROUP_BUY . '\'';
		$ext_info = unserialize($db->getOne($sql));
		$ext_info['restrict_amount'] = $val;
		$sql = 'UPDATE ' . $ecs->table('goods_activity') . ' SET ext_info = \'' . serialize($ext_info) . '\'' . (' WHERE act_id = \'' . $id . '\'');
		$db->query($sql);
		clear_cache_files();
		make_json_result($val);
	}
	
	else if ($_REQUEST['act'] == 'remove') {  //删除
		/*
		**********************
		$id = $_GET['id'];
		$sql = "DELETE FROM dsc_paipai_list WHERE ppj_id = {$id}";
		//var_dump($sql);
		$res = $db->query($sql);
		
		if($res>0){
			echo json_encode($res);
			exit;
		}
		**********************
		*/
		check_authz_json('group_by');
		$id = intval($_GET['id']);
		//$group_buy = paipai_buy_info($id, 0 , 'seller');
		//查询该商品是否有订单
		$sql = "select count(*) from " . $ecs->table("order_info") . (' where ppj_id =\'' . $id . '\'');
		$result = $db->getOne($sql);
		//判断是否有订单
		if($result>0){
			make_json_error($_LANG['error_exist_order']);
		}
		$sql = 'DELETE FROM ' . $ecs->table('paipai_list') . (' WHERE ppj_id = \'' . $id . '\' LIMIT 1');
		$db->query($sql);
		admin_log(addslashes($group_buy['goods_name']) . '[' . $id . ']', 'remove', 'group_buy');
		clear_cache_files();//清除缓存
		$url = 'paipai_buy.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
		ecs_header('Location: ' . $url . "\n");
		exit();
	}
}

?>
