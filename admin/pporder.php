<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/28 0028
 * Time: 15:06
 */
function get_pporder_goods($order)
{
    $goods_list = array();
    $goods_attr = array();

    $sql = 'SELECT goods_name,goods_sn,goods_number,goods_thumb FROM ' . $GLOBALS['ecs']->table('goods') . ' WHERE goods_id=' . $order['goods_id'];

    $res = $GLOBALS['db']->getRow($sql);

    return $res;

//    while ($row = $GLOBALS['db']->fetchRow($res)) {
//        if ($row['is_real'] == 0) {
//            $filename = ROOT_PATH . 'plugins/' . $row['extension_code'] . '/languages/common_' . $GLOBALS['_CFG']['lang'] . '.php';
//
//            if (file_exists($filename)) {
//                include_once $filename;
//
//                if (!empty($GLOBALS['_LANG'][$row['extension_code'] . '_link'])) {
//                    $row['goods_name'] = $row['goods_name'] . sprintf($GLOBALS['_LANG'][$row['extension_code'] . '_link'], $row['goods_id'], $row['order_sn']);
//                }
//            }
//        }
//
//        if (0 < $row['product_id']) {
//            $products = get_warehouse_id_attr_number($row['goods_id'], $row['goods_attr_id'], $row['ru_id'], $row['warehouse_id'], $row['area_id'], $row['model_attr']);
//            $row['storage'] = $products['product_number'];
//        }
//        else if ($row['model_inventory'] == 1) {
//            $row['storage'] = get_warehouse_area_goods($row['warehouse_id'], $row['goods_id'], 'warehouse_goods');
//        }
//        else if ($row['model_inventory'] == 2) {
//            $row['storage'] = get_warehouse_area_goods($row['area_id'], $row['goods_id'], 'warehouse_area_goods');
//        }
//
//        $row['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
//        $row['formated_subtotal'] = price_format($row['goods_price'] * $row['goods_number']);
//        $row['formated_goods_price'] = price_format($row['goods_price']);
//        $goods_attr[] = explode(' ', trim($row['goods_attr']));
//
//        if ($row['extension_code'] == 'package_buy') {
//            $row['storage'] = '';
//            $row['brand_name'] = '';
//            $row['package_goods_list'] = get_package_goods_list($row['goods_id']);
//            $activity = get_goods_activity_info($row['goods_id'], array('act_id', 'activity_thumb'));
//
//            if ($activity) {
//                $row['goods_thumb'] = get_image_path($row['goods_id'], $activity['activity_thumb'], true);
//            }
//        }
//
//        $sql = 'SELECT is_reality, is_return, is_fast FROM ' . $GLOBALS['ecs']->table('goods_extend') . ' WHERE goods_id = \'' . $row['goods_id'] . '\' LIMIT 1';
//        $goods_extend = $GLOBALS['db']->getRow($sql);
//        if ($row['is_reality'] == -1 && $goods_extend) {
//            $row['is_reality'] = $goods_extend['is_reality'];
//        }
//
//        if ($goods_extend['is_return'] == -1 && $goods_extend) {
//            $row['is_return'] = $goods_extend['is_return'];
//        }
//
//        if ($goods_extend['is_fast'] == -1 && $goods_extend) {
//            $row['is_fast'] = $goods_extend['is_fast'];
//        }
//
//        $sql = 'SELECT ret_id FROM ' . $GLOBALS['ecs']->table('order_return') . ' WHERE rec_id = \'' . $row['rec_id'] . '\'';
//        $row['ret_id'] = $GLOBALS['db']->getOne($sql);
//        $row['back_order'] = return_order_info($row['ret_id']);
//        $trade_id = find_snapshot($row['order_sn'], $row['goods_id']);
//
//        if ($trade_id) {
//            $row['trade_url'] = '../trade_snapshot.php?act=trade&tradeId=' . $trade_id . '&snapshot=true';
//        }
//
//        $row['product_id'] = empty($row['product_id']) ? 0 : $row['product_id'];
//        $goods_list[] = $row;
//    }
//
//    $attr = array();
//    $arr = array();
//
//    foreach ($goods_attr as $index => $array_val) {
//        foreach ($array_val as $value) {
//            $arr = explode(':', $value);
//            $attr[$index][] = array('name' => $arr[0], 'value' => $arr[1]);
//        }
//    }
//
//    return array('goods_list' => $goods_list, 'attr' => $attr);
}

//拍拍活动订单
function ppj_order_list($page = 0){


    $result = get_filter();  //admin\includes\lib_main.php文件的第619行,取得上次的过滤条件

    if ($result === false) { //

        $filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        $filter['list_type'] = !isset($_REQUEST['list_type']) ? 14 : intval($_REQUEST['list_type']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? ' DESC ' : trim($_REQUEST['sort_order']);

    }

    $filter['page'] = empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0 ? 1 : intval($_REQUEST['page']);

    if (0 < $page) {
        $filter['page'] = $page;
    }

    if (isset($_REQUEST['page_size']) && 0 < intval($_REQUEST['page_size'])) {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    }else {
        if (isset($_COOKIE['ECSCP']['page_size']) && 0 < intval($_COOKIE['ECSCP']['page_size'])) {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else {
            $filter['page_size'] = 15;
        }
    }


    if( $filter['list_type'] == '11'){    //卖方报名订单查询
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('paipai_goods_sellers');
        $record_count = $GLOBALS['db']->getOne($sql);

        $where=" WHERE 1 ";
        $sql=' SELECT pgs.user_id,pgs.ppjs_id,pgs.ppj_id,pgs.ppj_no,pgs.seller_min_fee,pgs.seller_max_fee,pgs.ls_ok,pgs.ls_staus,pl.goods_id,pl.ppj_name,pl.start_time,pl.end_time,pgs.createtime,u.user_name,u.mobile_phone,u.flag FROM ('. $GLOBALS['ecs']->table('paipai_goods_sellers'). 'as pgs'. ' LEFT JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON pgs.user_id = u.user_id) LEFT JOIN '.$GLOBALS['ecs']->table('paipai_list') .' AS pl ON pgs.ppj_id=pl.ppj_id AND pgs.ppj_no=pl.ppj_no '. $where . ' ORDER BY ' . 'pgs.ppjs_id' . $filter['sort_order'] . "  LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ', ' . $filter['page_size'];
        $row = $GLOBALS['db']->getAll($sql);
        for($i=0;$i<count($row);$i++){
            $row[$i]['id'] = $row[$i]['ppjs_id'];
            $row[$i]['short_order_time'] = local_date($GLOBALS['_CFG']['time_format'], $row[$i]['createtime']);
            $row[$i]['ls_staus'] =$row[$i]['ls_staus'] ==1?'SITSTATUS_SUCCESS':'SITSTATUS_FAIL';
            $row[$i]['ls_ok'] =$row[$i]['ls_ok'] ==0?'CLINCH_SUCCESS':'CLINCH_FAIL';
            $row[$i]['pp_status']=$row[$i]['end_time'] <time()?'PAIPAI_INVALID':'';
            $row[$i]['seller_min_fee']=price_format($row[$i]['seller_min_fee']);
            $row[$i]['seller_max_fee']=price_format($row[$i]['seller_max_fee']);
            //查询卖家信息
            $sql = ' SELECT user_name FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE user_id = \'' . $row[$i]['user_id'] . '\'';
            $sell_data = $GLOBALS['db']->getOne($sql, true);
            $row[$i]['sell'] = !empty($sell_data) ? $sell_data : $GLOBALS['_LANG']['anonymous'];
        }

    }else if( $filter['list_type'] == '12'){  //买方出价订单
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('paipai_goods_bid_user');
        $record_count = $GLOBALS['db']->getOne($sql);

        $where=" WHERE 1 ";
        $sql=' SELECT gb.bid_id,gb.user_id,gb.bid_price,gb.bid_time,gb.is_status,u.user_name,u.mobile_phone,u.flag,pl.ppj_name,pl.ppj_id,pl.ppj_no,pl.start_time,pl.end_time,pl.goods_id FROM ('. $GLOBALS['ecs']->table('paipai_goods_bid_user'). 'as gb'. ' LEFT JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON gb.user_id = u.user_id ) LEFT JOIN '.$GLOBALS['ecs']->table('paipai_list') .'AS pl ON gb.ppj_id=pl.ppj_id AND gb.ppj_no=pl.ppj_no'. $where . ' ORDER BY ' . 'gb.bid_id' . $filter['sort_order'] . "  LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ', ' . $filter['page_size'];
		$row = $GLOBALS['db']->getAll($sql);

        for($i=0;$i<count($row);$i++){
            $row[$i]['id'] = $row[$i]['bid_id'];
            $row[$i]['bid_price']=price_format($row[$i]['bid_price']);
            if($row[$i]['is_status'] =='0'){
                $row[$i]['is_status'] = 'BID_FAIL';
            }else if($row[$i]['is_status'] =='1'){
                $row[$i]['is_status'] = 'BID_SUCCESS';
            }else{
                $row[$i]['is_status'] = 'BID_ING';
            }
            $row[$i]['pp_status']=$row[$i]['end_time'] <time()?'PAIPAI_INVALID':'';
            $row[$i]['short_order_time'] = date('Y-m-d H:i:s',$row[$i]['bid_time']);
            //查询买家信息
            $sql = ' SELECT user_name FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE user_id = \'' . $row[$i]['user_id'] . '\'';
            $sell_data = $GLOBALS['db']->getOne($sql, true);
            $row[$i]['buyer'] = !empty($sell_data) ? $sell_data : $GLOBALS['_LANG']['anonymous'];
        }
   
    }else if( $filter['list_type'] == '13'){   //保证金支付列表
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('paipai_seller_pay_margin');
        $record_count = $GLOBALS['db']->getOne($sql);

        $where=" WHERE 1 ";
        $sql=' SELECT spm.spm_id,spm.user_id,spm.ppj_id,spm.ppj_no,spm.order_id,spm.pay_fee,spm.ls_pay_ok,spm.ls_refund,spm.paytime,u.user_name,u.mobile_phone,u.flag,pl.ppj_name,pl.goods_id,pl.end_time FROM ('. $GLOBALS['ecs']->table('paipai_seller_pay_margin'). 'as spm'. ' LEFT JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON spm.user_id = u.user_id )  LEFT JOIN' .$GLOBALS['ecs']->table('paipai_list') .'AS pl ON spm.ppj_id=pl.ppj_id AND spm.ppj_no=pl.ppj_no'. $where . ' ORDER BY ' . 'spm.spm_id ' . $filter['sort_order'] . "  LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ', ' . $filter['page_size'];
        $row = $GLOBALS['db']->getAll($sql);

        for($i=0;$i<count($row);$i++){
            $row[$i]['id'] = $row[$i]['spm_id'];
            $row[$i]['margin_price']=price_format($row[$i]['pay_fee']);
            if($row[$i]['ls_pay_ok'] =='1'){
                $row[$i]['ls_pay_ok'] = 'MARGIN_SUCCESS';
            }else{
                $row[$i]['ls_pay_ok'] = 'MARGIN_NOPAY';
            }
            if($row[$i]['ls_refund'] == '1'){
                $row[$i]['ls_refund'] = 'REFUND_SUCCESS';
            }elseif($row[$i]['ls_refund'] == '2'){
                $row[$i]['ls_refund'] = 'REFUND_FAIL';
                $re_sql="SELECT pm.refund_code,pm.refund_msg FROM ".$GLOBALS['ecs']->table('order_info')." AS o LEFT JOIN ".$GLOBALS['ecs']->table('paipai_margin_return')." AS pm ON o.order_sn=pm.order_id WHERE o.order_id=".$row[$i]['order_id'];
                $re_row = $GLOBALS['db']->getRow($re_sql);
                $row[$i]['refund_code'] = $re_row['refund_code'];
                $row[$i]['refund_msg'] = $re_row['refund_msg'];
            }else{
                $row[$i]['ls_refund'] = 'NOT_REFUND';
            }
            $local_time=time()-8*60*60;
            $row[$i]['pp_status']=$row[$i]['end_time'] <$local_time?'PAIPAI_INVALID':'';
            $row[$i]['short_order_time'] = $row[$i]['paytime'] ?date('Y-m-d H:i:s',$row[$i]['paytime']):'';
            //查询买家信息
            $sql = ' SELECT user_name FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE user_id = \'' . $row[$i]['user_id'] . '\'';
            $sell_data = $GLOBALS['db']->getOne($sql, true);
            $row[$i]['buyer'] = !empty($sell_data) ? $sell_data : $GLOBALS['_LANG']['anonymous'];
        }
    }else if( $filter['list_type'] >= '14'){        //匹配订单

        $sql_lt="SELECT ppj_endpay_time FROM ".$GLOBALS['ecs']->table('paipai_list')."  as ppj where ppj.ppj_id = o.ppj_id AND ppj.ppj_no = o.ppj_no";
        if($filter['list_type'] == '14'){        //匹配成功

            $pso_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_seller_ok')." WHERE status !='3' ";
            $pso_data = $GLOBALS['db']->getAll($pso_sql);
            $record_count = count($pso_data);

        }else if($filter['list_type'] == '15'){

            $pso_sql="SELECT * FROM ".$GLOBALS['ecs']->table('paipai_seller_ok')." WHERE status ='3' ";
            $pso_data = $GLOBALS['db']->getAll($pso_sql);
            $record_count = count($pso_data);

        }
         
        foreach($pso_data as $key => $val){
            $osql=" SELECT o.extension_code as oi_extension_code, o.order_id, o.main_order_id, o.order_sn, o.add_time,o.pay_time, o.order_status, o.shipping_status, o.pay_status, o.order_amount, o.money_paid, o.is_delete,o.shipping_fee, o.insure_fee, o.pay_fee, o.surplus,o.tax, o.integral_money, o.bonus, o.discount, o.coupons,o.shipping_time, o.auto_delivery_time, o.consignee, o.address, o.email, o.tel, o.mobile, o.extension_code as o_extension_code, o.extension_id, o.is_zc_order, o.pay_id, o.pay_name, o.referer, o.froms, o.user_id, o.chargeoff_status, o.confirm_take_time, o.shipping_id, o.shipping_name, o.goods_amount, ( o.goods_amount + o.tax + o.shipping_fee + o.insure_fee + o.pay_fee + o.pack_fee + o.card_fee ) AS total_fee, (o.goods_amount + o.tax + o.shipping_fee + o.insure_fee + o.pay_fee + o.pack_fee + o.card_fee - o.discount) AS total_fee_order,o.ppj_id,o.ppj_no,og.goods_id FROM ".$GLOBALS['ecs']->table('order_info')." AS o LEFT JOIN ".$GLOBALS['ecs']->table('order_goods')." AS og ON o.order_id = og.order_id WHERE o.order_id={$val['order_id']} GROUP BY o.order_id ORDER BY add_time DESC LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
            $order_data[] = $GLOBALS['db']->getAll($osql);
            $order_data[$key][0]['sell_id']=$val['user_id'];  //卖家id
            $order_data[$key][0]['goods_id']=$val['goods_id'];
            $order_data[$key][0]['ok_sellers_fee']=price_format($val['sellers_fee']);
            $order_data[$key][0]['ok_goods_nowprice']=price_format($val['goods_nowprice']);
            $order_data[$key][0]['ok_status']=$val['status'];
            $order_data[$key][0]['ok_confirm_time']=$val['confirm_time'];
        }
        if(empty($order_data)){
             exit;
        }

        foreach($order_data as $key2 => $val2){
            foreach($val2 as $key3=>$val3){
                $row[]=$val3;
            }
        }

        foreach ($row as $key => $value) {
            if ($value['shipping_status'] == 2 && empty($value['confirm_take_time'])) {
                $sql = 'SELECT MAX(log_time) AS log_time FROM ' . $GLOBALS['ecs']->table('order_action') . ' WHERE order_id = \'' . $value['order_id'] . '\' AND shipping_status = \'' . SS_RECEIVED . '\'';
                $confirm_take_time = $GLOBALS['db']->getOne($sql, true);
                $log_other = array('confirm_take_time' => $confirm_take_time);
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $log_other, 'UPDATE', 'order_id = \'' . $value['order_id'] . '\'');
                $value['confirm_take_time'] = $confirm_take_time;
            }

            if (($value['order_status'] == OS_UNCONFIRMED || $value['order_status'] == OS_CONFIRMED || $value['order_status'] == OS_SPLITED) && $value['pay_status'] == PS_UNPAYED) {
                $pay_log = get_pay_log($value['order_id'], 1);
                if ($pay_log && $pay_log['is_paid'] == 0) {
                    $payment = payment_info($value['pay_id']);
                    $file_pay = ROOT_PATH . 'includes/modules/payment/' . $payment['pay_code'] . '.php';
                    if ($payment && file_exists($file_pay)) {
                        include_once $file_pay;

                        if (class_exists($payment['pay_code'])) {
                            $pay_obj = new $payment['pay_code']();
                            $is_callable = array($pay_obj, 'query');

                            if (is_callable($is_callable)) {
                                $order_other = array('order_sn' => $value['order_sn'], 'log_id' => $pay_log['log_id']);
                                $pay_obj->query($order_other);
                                $sql = 'SELECT order_status, shipping_status, pay_status, pay_time FROM ' . $GLOBALS['ecs']->table('order_info') . ' WHERE order_id = \'' . $value['order_id'] . '\' LIMIT 1';
                                $order_info = $GLOBALS['db']->getRow($sql);

                                if ($order_info) {
                                    $value['order_status'] = $order_info['order_status'];
                                    $value['shipping_status'] = $order_info['shipping_status'];
                                    $value['pay_status'] = $order_info['pay_status'];
                                    $value['pay_time'] = $order_info['pay_time'];
                                }
                            }
                        }
                    }
                }
            }

            //查询卖家信息
            $sql = ' SELECT user_name FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE user_id = \'' . $value['sell_id'] . '\'';
            $value['sell'] = $GLOBALS['db']->getOne($sql, true);
            $row[$key]['sell'] = !empty($value['sell']) ? $value['sell'] : $GLOBALS['_LANG']['anonymous'];
            //查询买家信息
            $sql = ' SELECT user_name FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE user_id = \'' . $value['user_id'] . '\'';
            $value['buyer'] = $GLOBALS['db']->getOne($sql, true);

            $row[$key]['buyer'] = !empty($value['buyer']) ? $value['buyer'] : $GLOBALS['_LANG']['anonymous'];
            $row[$key]['formated_order_amount'] = price_format($value['order_amount']);
            $row[$key]['formated_money_paid'] = price_format($value['money_paid']);
            $row[$key]['formated_total_fee'] = price_format($value['total_fee']);
            $row[$key]['old_shipping_fee'] = $value['shipping_fee'];
            $row[$key]['shipping_fee'] = price_format($value['shipping_fee']);
            $row[$key]['short_order_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
            $row[$key]['formated_total_fee_order'] = price_format($value['total_fee_order']);
            $row[$key]['region'] = get_user_region_address($value['order_id']);

        }
    }

    foreach($row as $key=>$val){

        $goods = get_pporder_goods(array('goods_id'=>$val['goods_id']));
        $_goods_thumb = get_image_path($val['goods_id'], $goods['goods_thumb'], true);
        $row[$key]['goods_name'] = $goods['goods_name'];
        $row[$key]['goods_sn'] = $goods['goods_sn'];
        $row[$key]['goods_number'] = $goods['goods_number'];
        $row[$key]['goods_thumb'] = $_goods_thumb;

        //二次支付未加入order_goods表 所以默认为0 商户
        $ru_id=0;
        $ru_sql="SELECT shop_name FROM dsc_seller_shopinfo WHERE ru_id={$ru_id}";
        $ru_data=$GLOBALS['db']->getRow($ru_sql);
        $row[$key]['shop_name']=$ru_data['shop_name'];
    }

    //统一定义用户身份
    for($i=0;$i<count($row);$i++){
        if($row[$i]['flag'] =='0'){
            $row[$i]['flag_ch'] = '未定义';
        }
    }

    $filter['record_count'] = $record_count;
    $filter['page_count'] = 0 < $filter['record_count'] ? ceil($filter['record_count'] / $filter['page_size']) : 1;
    $arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}

// 执行方法

define('IN_ECS', true);
require dirname(__FILE__) . '/includes/init.php';
require_once ROOT_PATH . 'includes/lib_order.php';
require_once ROOT_PATH . 'includes/lib_goods.php';
$user_action_list = get_user_action_list($_SESSION['admin_id']);

admin_priv('group_by');
$adminru = get_admin_ru_id();
if ($adminru['ru_id'] == 0) {
    $smarty->assign('priv_ru', 1);
}
else {
    $smarty->assign('priv_ru', 0);
}



if ($_REQUEST['act'] == 'query') {

    admin_priv('order_view');
    $order_list = ppj_order_list();
    $priv_str = $db->getOne('SELECT action_list FROM ' . $ecs->table('admin_user') . ' WHERE user_id = \'' . $_SESSION['admin_id'] . '\'');

    if ($priv_str == 'all') {
        $smarty->assign('priv_str', $priv_str);
    }
    else {
        $smarty->assign('priv_str', $priv_str);
    }

    if (isset($_REQUEST['seller_list'])) {
        $seller_order = 1;
    }
    else {
        $seller_order = 0;
    }

    $list_type = isset($_POST['list_type']) ? $_POST['list_type'] : 14;
    $smarty->assign('list_type', $list_type);
    $smarty->assign('seller_order', $seller_order);
    $smarty->assign('action_link', array('href' => 'pporder.php?act=order_query', 'text' => $_LANG['03_order_query']));
    $smarty->assign('order_list', $order_list['orders']);
    $smarty->assign('filter', $order_list['filter']);
    $smarty->assign('record_count', $order_list['record_count']);
    $smarty->assign('count_arr', $order_list['count_arr']);
    $smarty->assign('page_count', $order_list['page_count']);
    $sort_flag = sort_flag($order_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    make_json_result($smarty->fetch('pporder_list.dwt'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));

}
else if ($_REQUEST['act'] == 'list') {

    admin_priv('order_view');
    $user_id = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    $store_id = !empty($_REQUEST['store_id']) ? intval($_REQUEST['store_id']) : 0;

    if ($store_id) {
        $smarty->assign('from_store', true);
    }
    $smarty->assign('user_id', $user_id);
    self_seller(BASENAME($_SERVER['PHP_SELF']));
    $smarty->assign('ur_here', $_LANG['02_order_list']);
    $smarty->assign('action_link', array('href' => 'pporder.php?act=order_query', 'text' => $_LANG['03_order_query']));
    $smarty->assign('action_link3', array('href' => 'javascript:download_orderlist();', 'text' => $_LANG['11_order_export']));

    if (0 < $user_id) {
        $smarty->assign('action_link2', array('href' => 'users.php?act=list', 'text' => $_LANG['02_paipai_list']));
    }

    $smarty->assign('status_list', $_LANG['cs']);
    $smarty->assign('os_unconfirmed', OS_UNCONFIRMED);
    $smarty->assign('cs_await_pay', CS_AWAIT_PAY);
    $smarty->assign('cs_await_ship', CS_AWAIT_SHIP);
    $smarty->assign('full_page', 1);
    $store_list = get_common_store_list();
    $smarty->assign('store_list', $store_list);
    $is_zc = !isset($_REQUEST['is_zc']) ? 0 : intval($_REQUEST['is_zc']);

    $list_type=!empty($_REQUEST['list_type']) ? intval($_REQUEST['list_type']) : 14;
    $smarty->assign('list_type', $list_type);



    $order_list = ppj_order_list();
  //  var_dump($order_list);
    $smarty->assign('order_list', $order_list['orders']);
    $is_zc = !isset($_REQUEST['is_zc']) ? 0 : intval($_REQUEST['is_zc']);
    $smarty->assign('is_zc', $is_zc);
    $smarty->assign('filter', $order_list['filter']);
    $smarty->assign('record_count', $order_list['record_count']);
    $smarty->assign('count_arr', $order_list['count_arr']);
    $smarty->assign('page_count', $order_list['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');
    $priv_str = $db->getOne('SELECT action_list FROM ' . $ecs->table('admin_user') . ' WHERE user_id = \'' . $_SESSION['admin_id'] . '\'');

    if ($priv_str == 'all') {
        $smarty->assign('priv_str', $priv_str);
    }
    else {
        $smarty->assign('priv_str', $priv_str);
    }

    assign_query_info();
    $smarty->display('pporder_list.dwt');
}