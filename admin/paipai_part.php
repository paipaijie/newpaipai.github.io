<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/28 0028
 * Time: 15:06
 */
function generateName(){
    $arrXing=array('赵','钱','孙','李','周','吴','郑','王','冯','陈','褚','卫','蒋','沈','韩','杨','朱','秦','尤','许','何','吕','施','张','孔','曹','严','华','金','魏','陶','姜','戚','谢','邹',
        '喻','柏','水','窦','章','云','苏','潘','葛','奚','范','彭','郎','鲁','韦','昌','马','苗','凤','花','方','任','袁','柳','鲍','史','唐','费','薛','雷','贺','倪','汤','滕','殷','罗',
        '毕','郝','安','常','傅','卞','齐','元','顾','孟','平','黄','穆','萧','尹','姚','邵','湛','汪','祁','毛','狄','米','伏','成','戴','谈','宋','茅','庞','熊','纪','舒','屈','项','祝',
        '董','梁','杜','阮','蓝','闵','季','贾','路','娄','江','童','颜','郭','梅','盛','林','钟','徐','邱','骆','高','夏','蔡','田','樊','胡','凌','霍','虞','万','支','柯','管','卢','莫',
        '柯','房','裘','缪','解','应','宗','丁','宣','邓','单','杭','洪','包','诸','左','石','崔','吉','龚','程','嵇','邢','裴','陆','荣','翁','荀','于','惠','甄','曲','封','储','仲','伊',
        '宁','仇','甘','武','符','刘','景','詹','龙','叶','幸','司','黎','溥','印','怀','蒲','邰','从','索','赖','卓','屠','池','乔','胥','闻','莘','党','翟','谭','贡','劳','逄','姬','申',
        '扶','堵','冉','宰','雍','桑','寿','通','燕','浦','尚','农','温','别','庄','晏','柴','瞿','阎','连','习','容','向','古','易','廖','庾','终','步','都','耿','满','弘','匡','国','文',
        '寇','广','禄','阙','东','欧','利','师','巩','聂','关','荆');
    $numbXing = count($arrXing);
    $arrMing=array('伟','刚','勇','毅','俊','峰','强','军','平','保','东','文','辉','力','明','永','健','世','广','志','义','兴','良','海','山','仁','波','宁','贵','福','生','龙','元','全'
    ,'国','胜','学','祥','才','发','武','新','利','清','飞','彬','富','顺','信','子','杰','涛','昌','成','康','星','光','天','达','安','岩','中','茂','进','林','有','坚','和','彪','博','诚'
    ,'先','敬','震','振','壮','会','思','群','豪','心','邦','承','乐','绍','功','松','善','厚','庆','磊','民','友','裕','河','哲','江','超','浩','亮','政','谦','亨','奇','固','之','轮','翰'
    ,'朗','伯','宏','言','若','鸣','朋','斌','梁','栋','维','启','克','伦','翔','旭','鹏','泽','晨','辰','士','以','建','家','致','树','炎','德','行','时','泰','盛','雄','琛','钧','冠','策'
    ,'腾','楠','榕','风','航','弘','秀','娟','英','华','慧','巧','美','娜','静','淑','惠','珠','翠','雅','芝','玉','萍','红','娥','玲','芬','芳','燕','彩','春','菊','兰','凤','洁','梅','琳'
    ,'素','云','莲','真','环','雪','荣','爱','妹','霞','香','月','莺','媛','艳','瑞','凡','佳','嘉','琼','勤','珍','贞','莉','桂','娣','叶','璧','璐','娅','琦','晶','妍','茜','秋','珊','莎'
    ,'锦','黛','青','倩','婷','姣','婉','娴','瑾','颖','露','瑶','怡','婵','雁','蓓','纨','仪','荷','丹','蓉','眉','君','琴','蕊','薇','菁','梦','岚','苑','婕','馨','瑗','琰','韵','融','园'
    ,'艺','咏','卿','聪','澜','纯','毓','悦','昭','冰','爽','琬','茗','羽','希','欣','飘','育','滢','馥','筠','柔','竹','霭','凝','晓','欢','霄','枫','芸','菲','寒','伊','亚','宜','可','姬'
    ,'舒','影','荔','枝','丽','阳','妮','宝','贝','初','程','梵','罡','恒','鸿','桦','骅','剑','娇','纪','宽','苛','灵','玛','媚','琪','晴','容','睿','烁','堂','唯','威','韦','雯','苇','萱'
    ,'阅','彦','宇','雨','洋','忠','宗','曼','紫','逸','贤','蝶','菡','绿','蓝','儿','翠','烟');
    $numbMing =  count($arrMing);

    $Xing = $arrXing[mt_rand(0,$numbXing-1)];
    $Ming = $arrMing[mt_rand(0,$numbMing-1)].$arrMing[mt_rand(0,$numbMing-1)];

    $name = $Xing.$Ming;

    return $name;

}
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
    for($i = 0; $i < 10; $i++) {
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
        $userdata=array('email'=>$val['email'],'user_name'=>$val['mobile'],'nick_name'=>$val['nick_name'],'password'=>'e10adc3949ba59abbe56e057f20f883e');
        $aduser=$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $userdata, 'INSERT');
    }
    $zusql="SELECT user_id,user_name FROM ".$GLOBALS['ecs']->table('users');
    $zu_data=$GLOBALS['db']->getRow($zusql);
    $usql="SELECT user_id,user_name FROM ".$GLOBALS['ecs']->table('users')."WHERE user_id >".$zu_data['user_id']."-10";
    $u_data=$GLOBALS['db']->getAll($usql);
    foreach($u_data as $key2=>$uval){

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
            $consignee=generateName();

            $adsdata=array(
                'user_id'=>$uval['user_id'],
                'mobile'=>$uval['user_name'],
                'consignee'=>$consignee,
                'country'=>'1',
                'province'=>$p_data['region_id'],
                'city'=>$act_c['region_id'],
                'district'=>$act_a['region_id'],
            );

            $aduser=$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_address'), $adsdata, 'INSERT');
    }


    $smarty->display('paipai_part_info.dwt');
}