<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/28 0028
 * Time: 15:06
 */
//function ppj_order_list($list_type){
//
//
//    $result = get_filter();  //admin\includes\lib_main.php文件的第619行,取得上次的过滤条件
//
//    if ($result === false) { //
//
//        $filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
//
//        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? ' DESC ' : trim($_REQUEST['sort_order']);
//
//        $filter['page'] = empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0 ? 1 : intval($_REQUEST['page']);
//
//    }
//
//    if (isset($_REQUEST['page_size']) && 0 < intval($_REQUEST['page_size'])) {
//        $filter['page_size'] = intval($_REQUEST['page_size']);
//    }else {
//        if (isset($_COOKIE['ECSCP']['page_size']) && 0 < intval($_COOKIE['ECSCP']['page_size'])) {
//            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
//        }
//        else {
//            $filter['page_size'] = 15;
//        }
//    }
//
//
//    if( $list_type == '11'){    //卖方报名订单查询
//        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('paipai_goods_sellers');
//        $record_count = $GLOBALS['db']->getOne($sql);
//
//        $where=" WHERE 1 ";
//        $sql=' SELECT pgs.ppjs_id,pgs.ppj_id,pgs.ppj_no,pgs.seller_min_fee,pgs.seller_max_fee,pgs.ls_ok,pl.ppj_name,pgs.createtime,u.user_name,u.mobile_phone,u.flag FROM ('. $GLOBALS['ecs']->table('paipai_goods_sellers'). 'as pgs'. ' LEFT JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON pgs.user_id = u.user_id) LEFT JOIN '.$GLOBALS['ecs']->table('paipai_list') .' AS pl ON pgs.ppj_id=pl.ppj_id AND pgs.ppj_no=pl.ppj_no '. $where . ' ORDER BY ' . 'pgs.ppjs_id' . $filter['sort_order'] . "  LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ', ' . $filter['page_size'];
//        $row = $GLOBALS['db']->getAll($sql);
//        for($i=0;$i<count($row);$i++){
//            $row[$i]['id'] = $row[$i]['ppjs_id'];
//            $row[$i]['createtime'] = date('Y-m-d H:i:s',$row[$i]['createtime']);
//            $row[$i]['ls_ok'] =$row[$i]['ls_ok'] ==1?'未成交':'成交';
//        }
//    }else if( $list_type == '12'){  //买方出价订单
//        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('paipai_goods_bid_user');
//        $record_count = $GLOBALS['db']->getOne($sql);
//
//        $where=" WHERE 1 ";
//        $sql=' SELECT gb.bid_id,gb.bid_price,gb.bid_time,gb.is_status,u.user_name,u.mobile_phone,u.flag,pl.ppj_name,pl.ppj_id,pl.ppj_no FROM ('. $GLOBALS['ecs']->table('paipai_goods_bid_user'). 'as gb'. ' LEFT JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON gb.user_id = u.user_id ) LEFT JOIN '.$GLOBALS['ecs']->table('paipai_list') .'AS pl ON gb.ppj_id=pl.ppj_id AND gb.ppj_no=pl.ppj_no'. $where . ' ORDER BY ' . 'gb.bid_id' . $filter['sort_order'] . "  LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ', ' . $filter['page_size'];
//        $row = $GLOBALS['db']->getAll($sql);
//
//        for($i=0;$i<count($row);$i++){
//            $row[$i]['id'] = $row[$i]['bid_id'];
//            if($row[$i]['is_status'] =='0'){
//                $row[$i]['is_status_ch'] = '出价进行中';
//            }else if($row[$i]['is_status'] =='1'){
//                $row[$i]['is_status_ch'] = '匹配成功';
//            }else{
//                $row[$i]['is_status_ch'] = '匹配失败';
//            }
//            $row[$i]['bid_time'] = date('Y-m-d H:i:s',$row[$i]['bid_time']);
//        }
//
//    }else if( $list_type == '13'){   //保证金支付列表
//        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('paipai_seller_pay_margin');
//        $record_count = $GLOBALS['db']->getOne($sql);
//
//        $where=" WHERE 1 ";
//        $sql=' SELECT spm.spm_id,spm.ppj_id,spm.ppj_no,spm.pay_fee,spm.ls_pay_ok,spm.ls_refund,spm.paytime,u.user_name,u.mobile_phone,u.flag,pl.ppj_name FROM ('. $GLOBALS['ecs']->table('paipai_seller_pay_margin'). 'as spm'. ' LEFT JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON spm.user_id = u.user_id )  LEFT JOIN' .$GLOBALS['ecs']->table('paipai_list') .'AS pl ON spm.ppj_id=pl.ppj_id AND spm.ppj_no=pl.ppj_no'.$where . ' ORDER BY ' . 'spm.spm_id' . $filter['sort_order'] . "  LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ', ' . $filter['page_size'];
//        $row = $GLOBALS['db']->getAll($sql);
//        for($i=0;$i<count($row);$i++){
//            $row[$i]['id'] = $row[$i]['spm_id'];
//            if($row[$i]['ls_pay_ok'] =='1'){
//                $row[$i]['ls_pay_ok_ch'] = '成功';
//            }
//            if($row[$i]['ls_refund'] == '1'){
//                $row[$i]['ls_refund_ch'] = '已退';
//            }else{
//                $row[$i]['ls_refund_ch'] = '未退';
//            }
//            $row[$i]['paytime'] = $row[$i]['paytime'] ?date('Y-m-d H:i:s',$row[$i]['paytime']):'';
//        }
//    }else if( $list_type >= '14'){        //匹配订单
//
//        $sql_lt="SELECT ppj_endpay_time FROM ".$GLOBALS['ecs']->table('paipai_list')."  as ppj where ppj.ppj_id = o.ppj_id AND ppj.ppj_no = o.ppj_no";
//        if($list_type == '14'){        //匹配成功
//
//            $pso_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_seller_ok')." WHERE status !='3' ";
//            $pso_data = $GLOBALS['db']->getAll($pso_sql);
//            $record_count = count($pso_data);
//
//        }else if($list_type == '15'){
//
//            $pso_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_seller_ok')." WHERE status ='3' ";
//            $pso_data = $GLOBALS['db']->getAll($pso_sql);
//            $record_count = count($pso_data);
//
//        }
//
//        foreach($pso_data as $key => $val){
//            $osql=" SELECT o.extension_code as oi_extension_code, o.order_id, o.main_order_id, o.order_sn, o.add_time,o.pay_time, o.order_status, o.shipping_status, o.pay_status, o.order_amount, o.money_paid, o.is_delete,o.shipping_fee, o.insure_fee, o.pay_fee, o.surplus,o.tax, o.integral_money, o.bonus, o.discount, o.coupons,o.shipping_time, o.auto_delivery_time, o.consignee, o.address, o.email, o.tel, o.mobile, o.extension_code as o_extension_code, o.extension_id, o.is_zc_order, o.pay_id, o.pay_name, o.referer, o.froms, o.user_id, o.chargeoff_status, o.confirm_take_time, o.shipping_id, o.shipping_name, o.goods_amount, ( o.goods_amount + o.tax + o.shipping_fee + o.insure_fee + o.pay_fee + o.pack_fee + o.card_fee ) AS total_fee, (o.goods_amount + o.tax + o.shipping_fee + o.insure_fee + o.pay_fee + o.pack_fee + o.card_fee - o.discount) AS total_fee_order,o.ppj_id,o.ppj_no FROM ".$GLOBALS['ecs']->table('order_info')." AS o LEFT JOIN ".$GLOBALS['ecs']->table('order_goods')." AS og ON o.order_id = og.order_id WHERE o.order_id={$val['order_id']} GROUP BY o.order_id ORDER BY add_time DESC LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
//            $order_data[] = $GLOBALS['db']->getAll($osql);
//        }
//
//        foreach($order_data as $key2 => $val2){
//            foreach($val2 as $key3=>$val3){
//                $row[]=$val3;
//            }
//        }
//
//        foreach ($row as $key => $value) {
//            if ($value['shipping_status'] == 2 && empty($value['confirm_take_time'])) {
//                $sql = 'SELECT MAX(log_time) AS log_time FROM ' . $GLOBALS['ecs']->table('order_action') . ' WHERE order_id = \'' . $value['order_id'] . '\' AND shipping_status = \'' . SS_RECEIVED . '\'';
//                $confirm_take_time = $GLOBALS['db']->getOne($sql, true);
//                $log_other = array('confirm_take_time' => $confirm_take_time);
//                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $log_other, 'UPDATE', 'order_id = \'' . $value['order_id'] . '\'');
//                $value['confirm_take_time'] = $confirm_take_time;
//            }
//
//            if (($value['order_status'] == OS_UNCONFIRMED || $value['order_status'] == OS_CONFIRMED || $value['order_status'] == OS_SPLITED) && $value['pay_status'] == PS_UNPAYED) {
//                $pay_log = get_pay_log($value['order_id'], 1);
//                if ($pay_log && $pay_log['is_paid'] == 0) {
//                    $payment = payment_info($value['pay_id']);
//                    $file_pay = ROOT_PATH . 'includes/modules/payment/' . $payment['pay_code'] . '.php';
//                    if ($payment && file_exists($file_pay)) {
//                        include_once $file_pay;
//
//                        if (class_exists($payment['pay_code'])) {
//                            $pay_obj = new $payment['pay_code']();
//                            $is_callable = array($pay_obj, 'query');
//
//                            if (is_callable($is_callable)) {
//                                $order_other = array('order_sn' => $value['order_sn'], 'log_id' => $pay_log['log_id']);
//                                $pay_obj->query($order_other);
//                                $sql = 'SELECT order_status, shipping_status, pay_status, pay_time FROM ' . $GLOBALS['ecs']->table('order_info') . ' WHERE order_id = \'' . $value['order_id'] . '\' LIMIT 1';
//                                $order_info = $GLOBALS['db']->getRow($sql);
//
//                                if ($order_info) {
//                                    $value['order_status'] = $order_info['order_status'];
//                                    $value['shipping_status'] = $order_info['shipping_status'];
//                                    $value['pay_status'] = $order_info['pay_status'];
//                                    $value['pay_time'] = $order_info['pay_time'];
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//
//            $sql = ' SELECT user_name FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE user_id = \'' . $value['user_id'] . '\'';
//            $value['buyer'] = $GLOBALS['db']->getOne($sql, true);
//
//            $row[$key]['buyer'] = !empty($value['buyer']) ? $value['buyer'] : $GLOBALS['_LANG']['anonymous'];
//            $row[$key]['formated_order_amount'] = price_format($value['order_amount']);
//            $row[$key]['formated_money_paid'] = price_format($value['money_paid']);
//            $row[$key]['formated_total_fee'] = price_format($value['total_fee']);
//            $row[$key]['old_shipping_fee'] = $value['shipping_fee'];
//            $row[$key]['shipping_fee'] = price_format($value['shipping_fee']);
//            $row[$key]['short_order_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
//            $row[$key]['formated_total_fee_order'] = price_format($value['total_fee_order']);
//            $row[$key]['region'] = get_user_region_address($value['order_id']);
//
//            $ok_sql="SELECT * FROM ". $GLOBALS['ecs']->table('paipai_seller_ok') . "WHERE order_id={$value['order_id']}" ;
//            $ok_data= $GLOBALS['db']->getRow($ok_sql);
//            $row[$key]['ok_sellers_fee'] = number_format($ok_data['sellers_fee'],2);
//            $row[$key]['ok_status'] = $ok_data['status'];
//            $row[$key]['ok_ppj_id'] = $ok_data['ppj_id'];
//            $row[$key]['ok_ppj_no'] = $ok_data['ppj_no'];
//
//            $order = array('order_id' => $value['order_id'], 'order_sn' => $value['order_sn']);
//            $goods = get_order_goods($order);
//            $row[$key]['goods_list'] = $goods['goods_list'];
//            $ru_sql="SELECT shop_name FROM dsc_seller_shopinfo WHERE ru_id={$goods['goods_list'][0]['ru_id']}";
//            $ru_data=$GLOBALS['db']->getRow($ru_sql);
//            $row[$key]['shop_name']=$ru_data['shop_name'];
//        }
//    }
//    //统一定义用户身份
//    for($i=0;$i<count($row);$i++){
//        if($row[$i]['flag'] =='0'){
//            $row[$i]['flag_ch'] = '未定义';
//        }
//        if($row[$i]['ok_status'] == '0'){
//            $row[$i]['ok_status_ch'] = '等待付款';
//        }else if($row[$i]['ok_status'] == '1'){
//            $row[$i]['ok_status_ch'] = '已付款';
//        }else if($row[$i]['ok_status'] == '2'){
//            $row[$i]['ok_status_ch'] = '已付款';
//        }else{
//            $row[$i]['ok_status_ch'] = '已失效';
//        }
//    }
//    $filter['record_count'] = $record_count;
//    $filter['page_count'] = 0 < $filter['record_count'] ? ceil($filter['record_count'] / $filter['page_size']) : 1;
//    $arr = array('orders' => $row, 'goods' => $goods,'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
//    return $arr;
//}

// 执行方法

define('IN_ECS', true);
require dirname(__FILE__) . '/includes/init.php';
require_once ROOT_PATH . 'includes/lib_order.php';
require_once ROOT_PATH . 'includes/lib_goods.php';
$adminru = get_admin_ru_id();
var_dump($_REQUEST['act']);
if ($_REQUEST['act'] == 'list') {

    admin_priv('order_view');
    $user_id = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    $store_id = !empty($_REQUEST['store_id']) ? intval($_REQUEST['store_id']) : 0;
    var_dump($user_id);
//
//    if ($store_id) {
//        $smarty->assign('from_store', true);
//    }
//    $smarty->assign('user_id', $user_id);
//    self_seller(BASENAME($_SERVER['PHP_SELF']));
//    $smarty->assign('ur_here', $_LANG['02_order_list']);
//    $smarty->assign('action_link', array('href' => 'order.php?act=order_query', 'text' => $_LANG['03_order_query']));
//    $smarty->assign('action_link3', array('href' => 'javascript:download_orderlist();', 'text' => $_LANG['11_order_export']));
//
//    if (0 < $user_id) {
//        $smarty->assign('action_link2', array('href' => 'users.php?act=list', 'text' => $_LANG['02_order_list']));
//    }
//
//    $smarty->assign('status_list', $_LANG['cs']);
//    $smarty->assign('os_unconfirmed', OS_UNCONFIRMED);
//    $smarty->assign('cs_await_pay', CS_AWAIT_PAY);
//    $smarty->assign('cs_await_ship', CS_AWAIT_SHIP);
//    $smarty->assign('full_page', 1);
//    $store_list = get_common_store_list();
//    $smarty->assign('store_list', $store_list);
//    $is_zc = !isset($_REQUEST['is_zc']) ? 0 : intval($_REQUEST['is_zc']);
//    $list_type=!empty($_REQUEST['list_type']) ? intval($_REQUEST['list_type']) : 1;
//    $smarty->assign('list_type', $list_type);
//
//    $order_list = ppj_order_list($list_type);
//    $smarty->assign('order_list', $order_list);
//    $is_zc = !isset($_REQUEST['is_zc']) ? 0 : intval($_REQUEST['is_zc']);
//    $smarty->assign('is_zc', $is_zc);
//    $smarty->assign('filter', $order_list['filter']);
//    $smarty->assign('record_count', $order_list['record_count']);
//    $smarty->assign('count_arr', $order_list['count_arr']);
//    $smarty->assign('page_count', $order_list['page_count']);
//    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');
//    $priv_str = $db->getOne('SELECT action_list FROM ' . $ecs->table('admin_user') . ' WHERE user_id = \'' . $_SESSION['admin_id'] . '\'');
//
//    if ($priv_str == 'all') {
//        $smarty->assign('priv_str', $priv_str);
//    }
//    else {
//        $smarty->assign('priv_str', $priv_str);
//    }
//
//    assign_query_info();
    $smarty->display('pporder_list.dwt');
}