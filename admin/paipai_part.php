<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/28 0028
 * Time: 15:06
 */


// 执行方法
define('IN_ECS', true);
require dirname(__FILE__) . '/includes/init.php';


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

/**
 * 方法执行开始
 * @ date
 * @ name
 */

if ($_REQUEST['act'] == 'list') {
    $smarty->assign('full_page', 1);
    $smarty->assign('ur_here', $_LANG['paipai_part_list']);

    $smarty->display('paipai_part_list.dwt');
}
elseif($_REQUEST['act'] == 'add'){
    $smarty->assign('full_page', 1);
    $smarty->assign('ur_here', $_LANG['paipai_part_add']);

    if($_POST['part_time']){

        $is_show = isset($_REQUEST['is_show']) ? $_REQUEST['is_show'] : 0;
        $part_time=$_POST['part_time'];

        if(strpos($part_time,':')== false){
            sys_msg($_LANG['error_symbol']);
        }
        $a=substr($part_time,strripos($part_time,":")+2);
        var_dump($a);
//        $part_data = array('part_time' => $part_time,'is_show' => $is_show);
//
//        $db->autoExecute($ecs->table('paipai_part_time'), $group_buy, 'INSERT');
//
//        admin_log(addslashes($goods_name), 'add', 'paipai_buy');
//
//        $links = array(

//            array('href' => 'paipai_part.php?act=add', 'text' => $_LANG['continue_add']),
//            array('href' => 'paipai_part.php?act=list', 'text' => $_LANG['back_list'])
//        );
//        sys_msg($_LANG['add_success'], 0, $links);
    }


    $smarty->display('paipai_part_info.dwt');
}