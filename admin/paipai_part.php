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

//    if($_POST['part_time']){
//
//        $is_show = isset($_REQUEST['is_show']) ? $_REQUEST['is_show'] : 0;
//        $part_time=$_POST['part_time'];
//
//        if(strpos($part_time,':')== false){
//            sys_msg($_LANG['error_symbol']);
//        }
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
//    }

    // 模拟用户添加
    $arr = array(
        130,131,132,133,134,135,136,137,138,139,
        144,147,
        150,151,152,153,155,156,157,158,159,
        176,177,178,
        180,181,182,183,184,185,186,187,188,189,
    );
    for($i = 0; $i < 100; $i++) {
        $tmp[] = $arr[array_rand($arr)].mt_rand(1000,9999).mt_rand(1000,9999);
    }
    $a=array();
    foreach($tmp as $val){
         $a[]['mobile']=$val;
    }
    foreach($a as $key=>$val){
        $data[$key]['mobile']=$val['mobile'];
        $data[$key]['email']=$val['mobile']."@163.com";
        $data[$key]['nick_name']="PPJ".substr($val['mobile'],-6);;
    }
    foreach($data as $key=>$val){
   // echo "<br/>";
    $p=array(2,3,4,5,6,7,8,10,11,12,13,14,15,16,17,18,22,23,24,25,26,27,31,32);
    $act_p=array_rand($p,1);

    $psql="SELECT * FROM ".$GLOBALS['ecs']->table('region')." WHERE region_id=".$act_p." AND region_type='1' ";
    $p_data=$GLOBALS['db']->getRow($psql);
  //  echo "省:";var_dump($p_data['region_id']);var_dump($p_data['region_name']); echo "<br/>";
    if($p_data['region_id']==''){
        $p_data['region_id']=31;
        $p_data['region_name']="浙江";
    }
    $csql="SELECT * FROM ".$GLOBALS['ecs']->table('region')." WHERE parent_id=".$p_data['region_id']." AND region_type='2' ";
    $c_data=$GLOBALS['db']->getAll($csql);
    $cact=array_rand($c_data,1);
    $act_c=$c_data[$cact];
  //  echo "市:";var_dump($act_c['region_id']);var_dump($act_c['region_name']); echo "<br/>";

    $asql="SELECT * FROM ".$GLOBALS['ecs']->table('region')." WHERE parent_id=".$act_c['region_id']. " AND region_type='3' ";
    $a_data=$GLOBALS['db']->getAll($asql);
    $aact=array_rand($a_data,1);
    $act_a=$a_data[$aact];
   // echo "区/县:";var_dump($act_a['region_id']);var_dump($act_a['region_name']);


        $data[$key]['p']= $p_data['region_name'];
        $data[$key]['c']= $act_c['region_name'];
        $data[$key]['a']= $act_a['region_name'];
    }
    var_dump($data);
    $smarty->display('paipai_part_info.dwt');
}