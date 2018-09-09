<?php

/**
 * DSC 采购订单程序
 * ============================================================================
 * * 版权所有 2005-2017 拍拍街网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liu $
 * $Id: wholesale_order.php 17217 2017-06-09 10:18:08Z liu $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');
require_once(ROOT_PATH . 'includes/lib_wholesale.php');
$exc   = new exchange($ecs->table("wholesale_order_info"), $db, 'order_id', 'order_sn');
$exc_goods   = new exchange($ecs->table("wholesale_order_goods"), $db, 'goods_id', 'goods_name');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
$ruCat = '';
if($adminru['ru_id'] == 0){
        $smarty->assign('priv_ru',   1);
}else{
        $smarty->assign('priv_ru',   0);
}
//ecmoban模板堂 --zhuo end

include_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/wholesale_purchase.php');
$smarty->assign('lang', $_LANG);
/*------------------------------------------------------ */
//-- 采购订单列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
	admin_priv('wholesale_order');
	
    $smarty->assign('ur_here',     $_LANG['02_wholesale_order']);
    $smarty->assign('full_page',  1);
	
    $list = wholesale_order_list();

    $smarty->assign('order_list',     $list['order_list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
	
    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('wholesale_order_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'query') 
{
	admin_priv('wholesale_order');
    $list = wholesale_order_list();

    //ecmoban模板堂 --zhuo start
    /* 获得该管理员的权限 */
    $priv_str = $db->getOne("SELECT action_list FROM " . $ecs->table('admin_user') . " WHERE user_id = '" . $_SESSION['admin_id'] . "'");

    /* 如果被编辑的管理员拥有了all这个权限，将不能编辑 */
    if ($priv_str == 'all') {
        $smarty->assign('priv_str', $priv_str);
    } else {
        $smarty->assign('priv_str', $priv_str);
    }

    /* 订单状态传值 */
    $composite_status = isset($_REQUEST['composite_status']) ? trim($_REQUEST['composite_status']) : -1;
    $smarty->assign('status', $composite_status);
    $smarty->assign('action_link', array('href' => 'order.php?act=order_query', 'text' => $_LANG['03_order_query']));
    //ecmoban模板堂 --zhuo end

    $smarty->assign('order_list',     $list['order_list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $sort_flag = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('wholesale_order_list.dwt'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 订单详情页面
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'info')
{
	admin_priv('wholesale_order');

    /* 根据订单id或订单号查询订单信息 */
    if (isset($_REQUEST['order_id']))
    {
        $order_id = intval($_REQUEST['order_id']);
        $order = wholesale_order_info($order_id);
    }
    elseif (isset($_REQUEST['order_sn']))
    {
        $order_sn = trim($_REQUEST['order_sn']);
        $order = wholesale_order_info(0, $order_sn);
    }
    else
    {
        /* 如果参数不存在，退出 */
        die('invalid parameter');
    }

	// var_dump($order);

    /* 如果订单不存在，退出 */
    if (empty($order))
    {
        die('order does not exist');
    }

    /* 取得用户名 */
    if ($order['user_id'] > 0)
    {
        $user = user_info($order['user_id']);
        if (!empty($user))
        {
            $order['user_name'] = $user['user_name'];
        }
    }
	

    /* 格式化金额 */
    if ($order['order_amount'] < 0)
    {
        $order['money_refund']          = abs($order['order_amount']);
        $order['formated_money_refund'] = price_format(abs($order['order_amount']));
    }

    /* 其他处理 */
    $order['order_time']    = local_date($_CFG['time_format'], $order['add_time']);
    // $order['pay_time']      = $order['pay_time'] > 0 ?
	//local_date($_CFG['time_format'], $order['pay_time']) : $_LANG['ps'][PS_UNPAYED];
    // $order['shipping_time'] = $order['shipping_time'] > 0 ?
	//local_date($_CFG['time_format'], $order['shipping_time']) : $_LANG['ss'][SS_UNSHIPPED];
    $order['status']        = $_LANG['os'][$order['order_status']];
	
    /* 此订单的发货备注(此订单的最后一条操作记录) */
    // $sql = "SELECT action_note FROM " . $ecs->table('order_action').
           // " WHERE order_id = '$order[order_id]' AND shipping_status = 1 ORDER BY log_time DESC";
    // $order['invoice_note'] = $db->getOne($sql);

    /* 取得订单商品总重量 */
    $weight_price = order_weight_price($order['order_id']);
    $order['total_weight'] = $weight_price['formated_weight'];
    
    $date = array('order_id');
        
	$order_child = count(get_table_date('wholesale_order_info', "main_order_id='".$order['order_id']."'", $date, 1));
	$order['order_child'] = $order_child;
	
    /* 参数赋值：订单 */
    $smarty->assign('order', $order);
    /* 取得用户信息 */  
    if ($order['user_id'] > 0)
    {
        /* 用户等级 */
        if ($user['user_rank'] > 0)
        {
            $where = " WHERE rank_id = '$user[user_rank]' ";
        }
        else
        {
            $where = " WHERE min_points <= " . intval($user['rank_points']) . " ORDER BY min_points DESC ";
        }
        $sql = "SELECT rank_name FROM " . $ecs->table('user_rank') . $where;
        $user['rank_name'] = $db->getOne($sql);

        // 地址信息
        $sql = "SELECT * FROM " . $ecs->table('user_address') . " WHERE user_id = '$order[user_id]'";
        $smarty->assign('address_list', $db->getAll($sql));
    }

    /* 取得订单商品及货品 */
    $goods_list = array();
    $goods_attr = array();
    $sql = " SELECT o.*, c.measure_unit, w.goods_number AS storage, g.model_inventory, g.model_attr as model_attr, o.goods_attr, g.suppliers_id, p.product_sn,
            g.user_id AS ru_id, g.brand_id, g.goods_thumb , g.bar_code 
            FROM " . $ecs->table('wholesale_order_goods') . " AS o
			LEFT JOIN " . $ecs->table('wholesale_products') . " AS p ON p.product_id = o.product_id
			LEFT JOIN " . $ecs->table('goods') . " AS g ON o.goods_id = g.goods_id
			LEFT JOIN " . $ecs->table('category') . " AS c ON g.cat_id = c.cat_id
			LEFT JOIN " . $ecs->table('wholesale') . " AS w ON w.goods_id = g.goods_id 
            WHERE o.order_id = '$order[order_id]' ";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
      
        $_goods_thumb = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $row['goods_thumb'] = $_goods_thumb;		
        
        $row['formated_subtotal']       = price_format($row['goods_price'] * $row['goods_number']);
        $row['formated_goods_price']    = price_format($row['goods_price']);
		

        $goods_attr[] = explode(' ', trim($row['goods_attr'])); //将商品属性拆分为一个数组

        $goods_list[] = $row;
    }
	
    $attr = array();
    $arr  = array();
    foreach ($goods_attr AS $index => $array_val)
    {
        foreach ($array_val AS $value)
        {
            $arr = explode(':', $value);//以 : 号将属性拆开
            $attr[$index][] =  @array('name' => $arr[0], 'value' => $arr[1]);
        }
    }
    $smarty->assign('goods_attr', $attr);
    $smarty->assign('goods_list', $goods_list);

	/**
     * 取得用户收货时间 以快物流信息显示为准，目前先用用户收货时间为准，后期修改TODO by Leah S
     */
    $sql = "SELECT log_time  FROM " . $ecs->table('order_action') . " WHERE order_id = '$order[order_id]' ";
    $res_time = local_date($_CFG['time_format'], $db->getOne($sql));
    $smarty->assign('res_time', $res_time);
    /**
     * by Leah E
     */


    
    
    
    
    /* 是否打印订单，分别赋值 */
    if (isset($_GET['print']))
    {   
        $smarty->assign('shop_name',    $store['shop_name']);
        $smarty->assign('shop_url',     $ecs->url());
        $smarty->assign('shop_address', $store['shop_address']);
        $smarty->assign('service_phone',$store['kf_tel']);
        $smarty->assign('print_time',   local_date($_CFG['time_format']));
        $smarty->assign('action_user',  $_SESSION['admin_name']);

        $smarty->template_dir = '../' . DATA_DIR;
        $smarty->display('order_print.html');
    }
    /* 打印快递单 */
    elseif (isset($_GET['shipping_print']))
    {
        //$smarty->assign('print_time',   local_date($_CFG['time_format']));
        //发货地址所在地
        $region_array = array();
        /*$region_id = !empty($store['country']) ? $store['country'] . ',' : '';
        $region_id .= !empty($store['province']) ? $store['province'] . ',' : '';
        $region_id .= !empty($store['city']) ? $store['city'] . ',' : '';
        $region_id = substr($region_id, 0, -1);
        //$region = $db->getAll("SELECT region_id, region_name FROM " . $ecs->table("region") . " WHERE region_id IN ($region_id)");*/
		$region = $db->getAll("SELECT region_id, region_name FROM " . $ecs->table("region")); //打印快递单地区 by wu
        if (!empty($region))
        {
            foreach($region as $region_data)
            {
                $region_array[$region_data['region_id']] = $region_data['region_name'];
            }
        }
        $smarty->assign('shop_name',    $store['shop_name']);
        $smarty->assign('order_id',    $order_id);
        $smarty->assign('province', $region_array[$store['province']]);
        $smarty->assign('city', $region_array[$store['city']]);
        $smarty->assign('district', $region_array[$store['district']]);
        $smarty->assign('shop_address', $store['shop_address']);
        $smarty->assign('service_phone',$store['kf_tel']);
        $shipping = $db->getRow("SELECT * FROM " . $ecs->table("shipping_tpl") . " WHERE shipping_id = '" . $order['shipping_id']."' and ru_id='".$order['ru_id']."'");
        //打印单模式
        if ($shipping['print_model'] == 2)
        {
            /* 可视化 */
            /* 快递单 */
            $shipping['print_bg'] = empty($shipping['print_bg']) ? '' : get_site_root_url() . $shipping['print_bg'];

            /* 取快递单背景宽高 */
            if (!empty($shipping['print_bg']))
            {
                $_size = @getimagesize($shipping['print_bg']);

                if ($_size != false)
                {
                    $shipping['print_bg_size'] = array('width' => $_size[0], 'height' => $_size[1]);
                }
            }

            if (empty($shipping['print_bg_size']))
            {
                $shipping['print_bg_size'] = array('width' => '1024', 'height' => '600');
            }

            /* 标签信息 */
            $lable_box = array();
            $lable_box['t_shop_country'] = $region_array[$store['country']]; //网店-国家
            $lable_box['t_shop_city'] = $region_array[$store['city']]; //网店-城市
            $lable_box['t_shop_province'] = $region_array[$store['province']]; //网店-省份          
			$sql = "select og.ru_id from " .$GLOBALS['ecs']->table('order_info') ." as oi ". "," .$GLOBALS['ecs']->table('order_goods'). " as og " .
					" where oi.order_id = og.order_id and oi.order_id = '" .$order['order_id']. "' group by oi.order_id";
			$ru_id = $GLOBALS['db']->getOne($sql);
			
			if($ru_id > 0){
				
				$sql = "select shoprz_brandName, shopNameSuffix from " .$GLOBALS['ecs']->table('merchants_shop_information') . " where user_id = '$ru_id'";
				$shop_info = $GLOBALS['db']->getRow($sql);
				
				$lable_box['t_shop_name'] = $shop_info['shoprz_brandName'] . $shop_info['shopNameSuffix']; //店铺-名称
			}else{
				$lable_box['t_shop_name'] = $_CFG['shop_name']; //网店-名称
			}
			
            $lable_box['t_shop_district'] = $region_array[$store['district']]; //网店-区/县
            $lable_box['t_shop_tel'] = $store['kf_tel']; //网店-联系电话
            $lable_box['t_shop_address'] = $store['shop_address']; //网店-地址
            $lable_box['t_customer_country'] = $region_array[$order['country']]; //收件人-国家
            $lable_box['t_customer_province'] = $region_array[$order['province']]; //收件人-省份
            $lable_box['t_customer_city'] = $region_array[$order['city']]; //收件人-城市
            $lable_box['t_customer_district'] = $region_array[$order['district']]; //收件人-区/县
            $lable_box['t_customer_tel'] = $order['tel']; //收件人-电话
            $lable_box['t_customer_mobel'] = $order['mobile']; //收件人-手机
            $lable_box['t_customer_post'] = $order['zipcode']; //收件人-邮编
            $lable_box['t_customer_address'] = $order['address']; //收件人-详细地址
            $lable_box['t_customer_name'] = $order['consignee']; //收件人-姓名

            $gmtime_utc_temp = gmtime(); //获取 UTC 时间戳
            $lable_box['t_year'] = date('Y', $gmtime_utc_temp); //年-当日日期
            $lable_box['t_months'] = date('m', $gmtime_utc_temp); //月-当日日期
            $lable_box['t_day'] = date('d', $gmtime_utc_temp); //日-当日日期

            $lable_box['t_order_no'] = $order['order_sn']; //订单号-订单
            $lable_box['t_order_postscript'] = $order['postscript']; //备注-订单
            $lable_box['t_order_best_time'] = $order['best_time']; //送货时间-订单
            $lable_box['t_pigeon'] = '√'; //√-对号
            $lable_box['t_custom_content'] = ''; //自定义内容

            //标签替换
            $temp_config_lable = explode('||,||', $shipping['config_lable']);
            if (!is_array($temp_config_lable))
            {
                $temp_config_lable[] = $shipping['config_lable'];
            }
            foreach ($temp_config_lable as $temp_key => $temp_lable)
            {
                $temp_info = explode(',', $temp_lable);
                if (is_array($temp_info))
                {
                    $temp_info[1] = $lable_box[$temp_info[0]];
                }
                $temp_config_lable[$temp_key] = implode(',', $temp_info);
            }
            $shipping['config_lable'] = implode('||,||',  $temp_config_lable);

            $smarty->assign('shipping', $shipping);

            $smarty->display('print.dwt');
        }
        elseif (!empty($shipping['shipping_print']))
        {
            /* 代码 */
            echo $smarty->fetch("str:" . $shipping['shipping_print']);
        }
        else
        {
            $shipping_code = $db->getOne("SELECT shipping_code FROM " . $ecs->table('shipping') . " WHERE shipping_id='" . $order['shipping_id']."'");		
			
            if ($shipping_code)
            {
                include_once(ROOT_PATH . 'includes/modules/shipping/' . $shipping_code . '.php');
            }

            if (!empty($_LANG['shipping_print']))
            {
                echo $smarty->fetch("str:$_LANG[shipping_print]");
            }
            else
            {
                echo $_LANG['no_print_shipping'];
            }
        }
    }
    else
    {
        /* 模板赋值 */
        $smarty->assign('ur_here', $_LANG['order_info']);
        $smarty->assign('action_link', array('href' => 'wholesale_order.php?act=list&' . list_link_postfix(), 'text' => $_LANG['02_order_list']));
        
        /* 显示模板 */
        assign_query_info();
        $smarty->display('wholesale_order_info.dwt');
    }
}

/*------------------------------------------------------ */
//-- 获取订单商品信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_goods_info')
{
    /* 取得订单商品 */
    $order_id = isset($_REQUEST['order_id'])?intval($_REQUEST['order_id']):0;
    if (empty($order_id))
    {
        make_json_response('', 1, $_LANG['error_get_goods_info']);
    }
    $goods_list = array();
    $goods_attr = array();
    $sql = "SELECT o.*, g.goods_thumb, g.goods_sn, g.brand_id, g.user_id AS ru_id, w.goods_number AS storage, g.model_inventory, o.goods_attr, oi.order_sn " .
            "FROM " . $ecs->table('wholesale_order_goods') . " AS o ".
            "LEFT JOIN " . $ecs->table('goods') . " AS g ON o.goods_id = g.goods_id " .
            "LEFT JOIN " . $ecs->table('wholesale') . " AS w ON w.goods_id = g.goods_id " .
            "LEFT JOIN " . $ecs->table('wholesale_order_info') . " AS oi ON oi.order_id = o.order_id " .
            "WHERE o.order_id = '{$order_id}' ";
    $res = $db->query($sql);
    
    while ($row = $db->fetchRow($res))
    {     
        if(empty($prod)){ //当商品没有属性库存时
            $row['goods_storage'] = $row['storage']; 
        }	
        $row['storage'] = !empty($row['goods_storage']) ? $row['goods_storage'] : 0;    	
        $row['formated_subtotal']       = price_format($row['goods_price'] * $row['goods_number']);
        $row['formated_goods_price']    = price_format($row['goods_price']);
        
        //图片显示
        $row['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        
        $goods_attr[] = explode(' ', trim($row['goods_attr'])); //将商品属性拆分为一个数组
        $goods_list[] = $row;
    }
    $attr = array();
    $arr  = array();
    foreach ($goods_attr AS $index => $array_val)
    {
        foreach ($array_val AS $value)
        {
            $arr = explode(':', $value);//以 : 号将属性拆开
            $attr[$index][] =  @array('name' => $arr[0], 'value' => $arr[1]);
        }
    }
    $smarty->assign('goods_attr', $attr);
    $smarty->assign('goods_list', $goods_list);
    $str = $smarty->fetch('show_order_goods.dwt');
    $goods[] = array('order_id' => $order_id, 'str' => $str);
    make_json_result($goods);
}

/*------------------------------------------------------ */
//-- 操作订单状态（载入页面）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'operate')
{	
    $order_id = '';
    /* 取得订单id（可能是多个，多个sn）和操作备注（可能没有） */
    if(isset($_REQUEST['order_id']))
    {
        $order_id= $_REQUEST['order_id'];
    }
	$order_id_list = explode(',', $order_id);
	
    $batch          = isset($_REQUEST['batch']); // 是否批处理
    $action_note    = isset($_REQUEST['action_note']) ? trim($_REQUEST['action_note']) : '';
	
	if(isset($_POST['remove'])){
		foreach ($order_id_list as $id_order)
		{
			/* 检查能否操作 */
			$order = wholesale_order_info($id_order , '');

			/* 删除订单 */
			$db->query("DELETE FROM ".$ecs->table('wholesale_order_info'). " WHERE order_id = '$order[order_id]'");
			$db->query("DELETE FROM ".$ecs->table('wholesale_order_goods'). " WHERE order_id = '$order[order_id]'");
			$db->query("DELETE FROM ".$ecs->table('wholesale_order_action'). " WHERE order_id = '$order[order_id]'");

			/* todo 记录日志 */
			admin_log($order['order_sn'], 'remove', 'wholesale_order');
		}
		/* 返回 */
		sys_msg($_LANG['order_removed'], 0, array(array('href'=>'wholesale_order.php?act=list&' . list_link_postfix(), 'text' => $_LANG['return_list'])));
	}
	/* 批量打印订单 */
    elseif (isset($_POST['print']))
    {	
        if (empty($order_id))
        {
            sys_msg($_LANG['pls_select_order']);
        }
		//快递鸟、电子面单 start
        $url = 'tp_api.php?act=order_print&order_sn='.$_POST['order_id'].'&order_type=wholesale_order';
        ecs_header("Location: $url\n");
        exit;
		//快递鸟、电子面单 end		
        
        /* 赋值公用信息 */
        $smarty->assign('print_time',   local_date($_CFG['time_format']));
        $smarty->assign('action_user',  $_SESSION['admin_name']);

        $html = '';
        $order_sn_list = explode(',', $_POST['order_id']);
        foreach ($order_sn_list as $order_sn)
        {
            /* 取得订单信息 */
            $order = order_info(0, $order_sn);
            
            /*判断是否是商家商品  by kong*/
            if($order['ru_id'] > 0){
                 //判断是否是主订单
                $date = array('order_id');
                $order_child = count(get_table_date('order_info', "main_order_id = '$order[order_id]'", $date, 1));
                if($order_child > 0){
                    $smarty->assign('shop_url',     $ecs->url());
                    $smarty->assign('shop_name',    $_CFG['shop_name']);
                    $smarty->assign('shop_address', $_CFG['shop_address']);
                    $smarty->assign('service_phone',$_CFG['service_phone']);
                }else{
                    $smarty->assign('shop_url',     $ecs->url());
                    $sql="SELECT domain_name FROM ".$ecs->table("seller_domain")." WHERE ru_id = '".$order['ru_id']."' AND  is_enable = 1";//获取商家域名
                    $domain_name = $db->getOne($sql);
                    $smarty->assign('domain_name',    $domain_name);
                    $smarty->assign('shop_name',   get_shop_name($order['ru_id'], 1));
                    $seller_shopinfo=$db->getRow("SELECT shop_address ,kf_tel FROM".$ecs->table("seller_shopinfo")." WHERE ru_id = '$order[ru_id]' LIMIT 1");//获取商家地址，电话
                    $smarty->assign('shop_address', $seller_shopinfo['shop_address']);
                    $smarty->assign('service_phone',$seller_shopinfo['kf_tel']);
                }
            }else{
                $smarty->assign('shop_url',     $ecs->url());
                $smarty->assign('shop_name',    $_CFG['shop_name']);
                $smarty->assign('shop_address', $_CFG['shop_address']);
                $smarty->assign('service_phone',$_CFG['service_phone']);
            }
            // by kong end
            if (empty($order))
            {
                continue;
            }

            /* 根据订单是否完成检查权限 */
            if (order_finished($order))
            {
                if (!admin_priv('order_view_finished', '', false))
                {
                    continue;
                }
            }
            else
            {
                if (!admin_priv('order_view', '', false))
                {
                    continue;
                }
            }

            /* 如果管理员属于某个办事处，检查该订单是否也属于这个办事处 */
            $sql = "SELECT agency_id FROM " . $ecs->table('admin_user') . " WHERE user_id = '$_SESSION[admin_id]'";
            $agency_id = $db->getOne($sql);
            if ($agency_id > 0)
            {
                if ($order['agency_id'] != $agency_id)
                {
                    continue;
                }
            }

            /* 取得用户名 */
            if ($order['user_id'] > 0)
            {
                $user = user_info($order['user_id']);
                if (!empty($user))
                {
                    $order['user_name'] = $user['user_name'];
                }
            }

            /* 取得区域名 */
            $order['region'] = get_user_region_address($order['order_id']);

            /* 其他处理 */
            $order['order_time']    = local_date($_CFG['time_format'], $order['add_time']);
            $order['pay_time']      = $order['pay_time'] > 0 ?
                local_date($_CFG['time_format'], $order['pay_time']) : $_LANG['ps'][PS_UNPAYED];
            $order['shipping_time'] = $order['shipping_time'] > 0 ?
                local_date($_CFG['time_format'], $order['shipping_time']) : $_LANG['ss'][SS_UNSHIPPED];
            $order['status']        = $_LANG['os'][$order['order_status']] . ',' . $_LANG['ps'][$order['pay_status']] . ',' . $_LANG['ss'][$order['shipping_status']];
            $order['invoice_no']    = $order['shipping_status'] == SS_UNSHIPPED || $order['shipping_status'] == SS_PREPARING ? $_LANG['ss'][SS_UNSHIPPED] : $order['invoice_no'];

            /* 此订单的发货备注(此订单的最后一条操作记录) */
            $sql = "SELECT action_note FROM " . $ecs->table('order_action').
                   " WHERE order_id = '$order[order_id]' AND shipping_status = 1 ORDER BY log_time DESC";
            $order['invoice_note'] = $db->getOne($sql);

            /* 参数赋值：订单 */
            $smarty->assign('order', $order);

            /* 取得订单商品 */
            $goods_list = array();
            $goods_attr = array();
            $sql = "SELECT o.*, c.measure_unit, g.goods_unit, g.goods_number AS storage, o.goods_attr, IFNULL(b.brand_name, '') AS brand_name, g.bar_code " .
                    "FROM " . $ecs->table('order_goods') . " AS o ".
                    "LEFT JOIN " . $ecs->table('goods') . " AS g ON o.goods_id = g.goods_id " .
                    "LEFT JOIN " . $ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
                    'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS c ON g.cat_id = c.cat_id ' .
                    "WHERE o.order_id = '$order[order_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $products = get_warehouse_id_attr_number($row['goods_id'], $row['goods_attr_id'], $row['ru_id'], $row['warehouse_id'], $row['area_id'], $row['model_attr']);
                if($row['product_id']){
                    $row['bar_code'] = $products['bar_code'];
                }
        
                /* 虚拟商品支持 */
                if ($row['is_real'] == 0)
                {
                    /* 取得语言项 */
                    $filename = ROOT_PATH . 'plugins/' . $row['extension_code'] . '/languages/common_' . $_CFG['lang'] . '.php';
                    if (file_exists($filename))
                    {
                        include_once($filename);
                        if (!empty($_LANG[$row['extension_code'].'_link']))
                        {
                            $row['goods_name'] = $row['goods_name'] . sprintf($_LANG[$row['extension_code'].'_link'], $row['goods_id'], $order['order_sn']);
                        }
                    }
                }

                $row['formated_subtotal']       = price_format($row['goods_price'] * $row['goods_number']);
                $row['formated_goods_price']    = price_format($row['goods_price']);
				$row['measure_unit']			= $row['goods_unit'] ? $row['goods_unit'] : $row['measure_unit'];

                $goods_attr[] = explode(' ', trim($row['goods_attr'])); //将商品属性拆分为一个数组
                $goods_list[] = $row;
            }

            $attr = array();
            $arr  = array();
            foreach ($goods_attr AS $index => $array_val)
            {
                foreach ($array_val AS $value)
                {
                    $arr = explode(':', $value);//以 : 号将属性拆开
                    $attr[$index][] =  @array('name' => $arr[0], 'value' => $arr[1]);
                }
            }
            $smarty->assign('goods_attr', $attr);
            $smarty->assign('goods_list', $goods_list);
            $smarty->template_dir = '../' . DATA_DIR;
            $html .= $smarty->fetch('order_print.html') .
                '<div style="PAGE-BREAK-AFTER:always"></div>';
        }
		
        echo $html;
        exit;
    }
	
}elseif($_REQUEST['act'] == 'pay_order'){
    require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => 0,'msg'=>'');
    
    $order_id = !empty($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;
    if($order_id > 0){
        $sql = "SELECT pay_status FROM".$ecs->table('wholesale_order_info')."WHERE order_id = '$order_id'";
        $pay_status = $db->getOne($sql);
        if($pay_status == 1){
             /* 已付款则退出 */
            $result['error'] = 1;
            $result['msg'] = '不能重复付款！';
        }else{
            require(ROOT_PATH . '/includes/lib_payment.php');
            $sql = "SELECT log_id FROM".$ecs->table('pay_log')."WHERE order_id = '$order_id' AND order_type = '". PAY_WHOLESALE ."'";
            $log_id = $db->getOne($sql);
            order_paid($log_id,1);
        }
    }else{
         /* 如果参数不存在，退出 */
        $result['error'] = 1;
        $result['msg'] = 'invalid parameter';
    }
    
    die($json->encode($result));
}

/* 获取采购订单列表 */
function wholesale_order_list()
{
    //ecmoban模板堂 --zhuo start
    $adminru = get_admin_ru_id();
    $ruCat = '';
    $no_main_order = '';
    $noTime = gmtime();
    //ecmoban模板堂 --zhuo end

    $result = get_filter();
    if ($result === false)
    {
        /* 过滤信息 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
        $filter['consignee'] = empty($_REQUEST['consignee']) ? '' : trim($_REQUEST['consignee']);
        $filter['address'] = empty($_REQUEST['address']) ? '' : trim($_REQUEST['address']);
        
        if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
            $filter['order_sn'] = json_str_iconv($filter['order_sn']);
            $filter['consignee'] = json_str_iconv($filter['consignee']);
            $filter['address'] = json_str_iconv($filter['address']);
        }

        $filter['email'] = empty($_REQUEST['email']) ? '' : trim($_REQUEST['email']);
        $filter['zipcode'] = empty($_REQUEST['zipcode']) ? '' : trim($_REQUEST['zipcode']);
        $filter['tel'] = empty($_REQUEST['tel']) ? '' : trim($_REQUEST['tel']);
        $filter['mobile'] = empty($_REQUEST['mobile']) ? 0 : trim($_REQUEST['mobile']);
        $filter['country'] = empty($_REQUEST['order_country']) ? 0 : intval($_REQUEST['order_country']);
        $filter['province'] = empty($_REQUEST['order_province']) ? 0 : intval($_REQUEST['order_province']);
        $filter['city'] = empty($_REQUEST['order_city']) ? 0 : intval($_REQUEST['order_city']);
        $filter['district'] = empty($_REQUEST['order_district']) ? 0 : intval($_REQUEST['order_district']);
        $filter['street'] = empty($_REQUEST['order_street']) ? 0 : intval($_REQUEST['order_street']);
        $filter['shipping_id'] = empty($_REQUEST['shipping_id']) ? 0 : intval($_REQUEST['shipping_id']);
        $filter['pay_id'] = empty($_REQUEST['pay_id']) ? 0 : intval($_REQUEST['pay_id']);
        $filter['order_status'] = isset($_REQUEST['order_status']) ? intval($_REQUEST['order_status']) : -1;
        $filter['shipping_status'] = isset($_REQUEST['shipping_status']) ? intval($_REQUEST['shipping_status']) : -1;
        $filter['pay_status'] = isset($_REQUEST['pay_status']) ? intval($_REQUEST['pay_status']) : -1;
        $filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
        $filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);
        $filter['composite_status'] = isset($_REQUEST['composite_status']) ? intval($_REQUEST['composite_status']) : -1;

        $filter['source'] = empty($_REQUEST['source']) ? '' : trim($_REQUEST['source']); //来源起始页

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
        $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);
        $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
		
        $where = ' WHERE 1 ';
        
        if($filter['keywords']){
            $where  .= " AND (o.order_sn LIKE '%" .$filter['keywords']. "%'";
            $where  .= " OR (iog.goods_name LIKE '%" .$filter['keywords']. "%' OR iog.goods_sn LIKE '%" .$filter['keywords']. "%'))";
        }
        
        if($adminru['ru_id'] > 0){
            $where .= " AND (SELECT og.ru_id FROM " . $GLOBALS['ecs']->table('wholesale_order_goods') .' as og' . " WHERE og.order_id = o.order_id LIMIT 1) = '" .$adminru['ru_id']. "' ";
        }

        if($filter['source'] == 'start' || $adminru['ru_id'] > 0 || $filter['order_type'] == 2 || $filter['keywords'])
        {
            $no_main_order = " and (select count(*) from " .$GLOBALS['ecs']->table('wholesale_order_info'). " as oi2 where oi2.main_order_id = o.order_id) = 0 ";  //主订单下有子订单时，则主订单不显示
        }
        
        $leftJoin = '';
        
        if ($filter['order_sn'])
        {
            $where .= " AND o.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
        }
        if ($filter['consignee'])
        {
            $where .= " AND o.consignee LIKE '%" . mysql_like_quote($filter['consignee']) . "%'";
        }
        if ($filter['email'])
        {
            $where .= " AND o.email LIKE '%" . mysql_like_quote($filter['email']) . "%'";
        }
        if ($filter['address'])
        {
            $where .= " AND o.address LIKE '%" . mysql_like_quote($filter['address']) . "%'";
        }
        if ($filter['zipcode'])
        {
            $where .= " AND o.zipcode LIKE '%" . mysql_like_quote($filter['zipcode']) . "%'";
        }
        if ($filter['tel'])
        {
            $where .= " AND o.tel LIKE '%" . mysql_like_quote($filter['tel']) . "%'";
        }
        if ($filter['mobile'])
        {
            $where .= " AND o.mobile LIKE '%" .mysql_like_quote($filter['mobile']) . "%'";
        }
        if ($filter['country'])
        {
            $where .= " AND o.country = '$filter[country]'";
        }
        if ($filter['province'])
        {
            $where .= " AND o.province = '$filter[province]'";
        }
        if ($filter['city'])
        {
            $where .= " AND o.city = '$filter[city]'";
        }
        if ($filter['district'])
        {
            $where .= " AND o.district = '$filter[district]'";
        }
        if ($filter['street'])
        {
            $where .= " AND o.street = '$filter[street]'";
        }
        if ($filter['shipping_id'])
        {
            $where .= " AND o.shipping_id  = '$filter[shipping_id]'";
        }
        if ($filter['pay_id'])
        {
            $where .= " AND o.pay_id  = '$filter[pay_id]'";
        }
        if ($filter['order_status'] != -1)
        {
            $where .= " AND o.order_status  = '$filter[order_status]'";
        }
        if ($filter['shipping_status'] != -1)
        {
            $where .= " AND o.shipping_status = '$filter[shipping_status]'";
        }
        if ($filter['pay_status'] != -1)
        {
            $where .= " AND o.pay_status = '$filter[pay_status]'";
        }
        if ($filter['user_id'])
        {
            $where .= " AND o.user_id = '$filter[user_id]'";
        }
        if ($filter['user_name'])
        {
            $where .= " AND (SELECT u.user_id FROM " .$GLOBALS['ecs']->table('users'). " AS u WHERE u.user_name LIKE '%" . mysql_like_quote($filter['user_name']) . "%' LIMIT 1) = o.user_id";
        }
        if ($filter['start_time'])
        {
            $where .= " AND o.add_time >= '$filter[start_time]'";
        }
        if ($filter['end_time'])
        {
            $where .= " AND o.add_time <= '$filter[end_time]'";
        }

        /*  @author-bylu 确认收货时间 start   */
        if ($filter['start_take_time'])
        {
            $where .= " AND oa.log_time >= '$filter[start_take_time]'";
        }
        if ($filter['end_take_time'])
        {
            $where .= " AND oa.log_time <= '$filter[end_take_time]'";
        }		

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }
		
        $where_store = '';
        if (empty($filter['start_take_time']) || empty($filter['end_take_time'])) {
            if ($store_search == 0 && $adminru['ru_id'] == 0) {
                $where_store = " AND (SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('wholesale_order_goods') . " AS og " . " WHERE o.order_id = og.order_id LIMIT 1) > 0 " .
                        " AND (SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('wholesale_order_info') . " AS oi2 WHERE oi2.main_order_id = o.order_id) = 0";
            }
        }

        /* 记录总数 */    
        if(!empty($filter['start_take_time']) || !empty($filter['end_take_time']))
        {
            $sql = "SELECT o.order_id FROM " . $GLOBALS['ecs']->table('wholesale_order_info') . " AS o " .
                    $leftJoin .
                    "LEFT JOIN " . $GLOBALS['ecs']->table('wholesale_order_action') . " AS oa ON o.order_id = oa.order_id " .
                    $where . $where_store . $no_main_order . " GROUP BY o.order_id";

            $record_count = count($GLOBALS['db']->getAll($sql));
        }
        elseif(!empty($filter['keywords']))
        {
            $leftJoin .= " LEFT JOIN " . $GLOBALS['ecs']->table('wholesale_order_goods') . " AS iog ON iog.order_id = o.order_id ";

            $sql = "SELECT o.order_id FROM " . $GLOBALS['ecs']->table('wholesale_order_info') . " AS o " .
                    $leftJoin .
                    "LEFT JOIN " . $GLOBALS['ecs']->table('wholesale_order_action') . " AS oa ON o.order_id = oa.order_id " .
                    $where . $where_store . $no_main_order . " GROUP BY o.order_id";

            $record_count = count($GLOBALS['db']->getAll($sql));
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('wholesale_order_info') . " AS o " .
                    $leftJoin .
                    $where . $where_store . $no_main_order;

            $record_count = $GLOBALS['db']->getOne($sql);
        }
        
        $filter['record_count']   = $record_count;
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
        
        if(!empty($filter['keywords']) && empty($filter['user_name']))
        {
            $groupBy = " GROUP BY o.order_id ";
        }
        else
        {
            $groupBy = " GROUP BY o.order_id ";
            $leftJoin .= " LEFT JOIN " .$GLOBALS['ecs']->table('wholesale_order_action'). " AS oa ON o.order_id = oa.order_id ";
        }
          
        /* 查询 */
        $sql = "SELECT o.order_id, o.main_order_id, o.order_sn, o.add_time, o.order_status," .
                " o.consignee, o.address, o.email, o.mobile, o.order_amount, o.is_delete,o.pay_id,o.pay_fee,o.pay_time,o.pay_status, " .
                " o.user_id " . 
                " FROM " . $GLOBALS['ecs']->table('wholesale_order_info') . " AS o " .
                $leftJoin .
                $where . $where_store . $no_main_order . $groupBy .
                " ORDER BY $filter[sort_by] $filter[sort_order] " .
                " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";

        foreach (array('order_sn', 'consignee', 'email', 'address', 'zipcode', 'tel', 'user_name') AS $val)
        {
            $filter[$val] = stripslashes($filter[$val]);
        }

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    /* 格式话数据 */
    foreach ($row AS $key => $value)
    {
        $row[$key]['pay_name'] = $GLOBALS['db']->getOne("SELECT pay_name FROM".$GLOBALS['ecs']->table('payment')."WHERE pay_id = '" . $value['pay_id']. "'");
        $row[$key]['pay_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['pay_time']);
        //查商家ID
        $value['ru_id'] = $GLOBALS['db']->getOne(" SELECT ru_id FROM ".$GLOBALS['ecs']->table('wholesale_order_goods')." WHERE order_id = '".$value['order_id']."'", true);
		//查会员名称
        $sql = " SELECT user_name FROM ".$GLOBALS['ecs']->table('users')." WHERE user_id = '".$value['user_id']."'";
        $value['buyer'] = $GLOBALS['db']->getOne($sql, true);
        $row[$key]['buyer'] = !empty($value['buyer']) ? $value['buyer'] : $GLOBALS['_LANG']['anonymous'];
		
        $row[$key]['formated_order_amount'] = price_format($value['order_amount']);
        $row[$key]['formated_money_paid'] = price_format($value['money_paid']);
        $row[$key]['formated_total_fee'] = price_format($value['total_fee']);
        $row[$key]['short_order_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
        $row[$key]['formated_total_fee_order'] = price_format($value['total_fee_order']);
        
        /* 取得区域名 */
        $row[$key]['region'] = get_user_region_address($value['order_id']);

        //ecmoban模板堂 --zhuo start
        $row[$key]['user_name'] = get_shop_name($value['ru_id'], 1);

        $order_id = $value['order_id'];
        $date = array('order_id');
        
        $order_child = count(get_table_date('wholesale_order_info', "main_order_id='$order_id'", $date, 1));
        $row[$key]['order_child'] = $order_child;

        $date = array('order_sn');
        $child_list = get_table_date('wholesale_order_info', "main_order_id='$order_id'", $date, 1);
        $row[$key]['child_list'] = $child_list;
        
        if(!empty($child_list)){
            $row[$key]['shop_name'] = $GLOBALS["_LANG"]['to_order_sn2'];
        }else{
            $row[$key]['shop_name'] = $row[$key]['user_name'];
        }
        
        //ecmoban模板堂 --zhuo end
        if ($value['order_status'] == OS_INVALID || $value['order_status'] == OS_CANCELED)
        {
            /* 如果该订单为无效或取消则显示删除链接 */
            $row[$key]['can_remove'] = 1;
        }
        else
        {
            $row[$key]['can_remove'] = 0;
        }
    }
	
    $arr = array('order_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>