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
function grouping($total,$num,$area){
    $average = round($total / $num);
    $sum = 0;
    $result = array_fill( 1, $num, 0 );

    for( $i = 1; $i < $num; $i++ ){
        //根据已产生的随机数情况，调整新随机数范围，以保证各份间差值在指定范围内
        if( $sum > 0 ){
            $max = 0;
            $min = 0 - round( $area / 2 );
        }elseif( $sum < 0 ){
            $min = 0;
            $max = round( $area / 2 );
        }else{
            $max = round( $area / 2 );
            $min = 0 - round( $area / 2 );
        }

        //产生各份的份额
        $random = rand( $min, $max );
        $sum += $random;
        $result[$i] = $average + $random;
    }

//最后一份的份额由前面的结果决定，以保证各份的总和为指定值
    $result[$num] = $average - $sum;

    return $result;
}

/**
 * @param $sale_data
 * @param $mouth
 */
function order_add($sale_data, $mouth,$batch_number){
    $year='2018';
    $days =cal_days_in_month(CAL_GREGORIAN, $mouth, $year);
    $oi_sql = "INSERT INTO ".$GLOBALS['ecs']->table('order_info')." (order_sn,user_id,order_status,shipping_status,pay_status,consignee,country,province,city,district,mobile,pay_id,pay_name,goods_amount,money_paid,order_amount,add_time,confirm_time,pay_time,shipping_time,confirm_take_time) VALUES ";
    $og_sql="INSERT INTO ".$GLOBALS['ecs']->table('order_goods')."(user_id,goods_id,goods_name,goods_sn,market_price,goods_price,is_real,warehouse_id,area_id) VALUES";
    foreach($sale_data as $key=>$val){
        //买家
        $user_id=rand(40079,861288);
        $u_sql='SELECT u.user_id,ua.consignee,ua.country,ua.province,ua.city,ua.district,ua.mobile FROM '.$GLOBALS['ecs']->table('users').' AS u LEFT JOIN '.$GLOBALS['ecs']->table('user_address').'AS ua ON u.user_id=ua.user_id  WHERE u.user_id='.$user_id;
        $user_row=$GLOBALS['db']->getRow($u_sql);
        //商品
        $g_sql='SELECT goods_id,goods_name,goods_sn,market_price,shop_price FROM '.$GLOBALS['ecs']->table('goods').' WHERE goods_id='.$val['goods_id'];
        $goods_row=$GLOBALS['db']->getRow($g_sql);
        $d=rand(1,$days);
        if($d<10){
            $d='0'.$d;
        }
        $order_sn=$year.$mouth.$d.str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        $order_time=$year.'-'.$mouth.'-'.$d;
        $S=rand(8,22);//随机--时
        if($S<10){
            $S='0'.$S;
        }
        $F=rand(0,59);//随机--分
        if($F<10){
            $F='0'.$F;
        }
        $M=rand(0,50);//随机--秒
        if($M<10){
            $M='0'.$M;
        }
        $SC=$S+1;
        $MC=$M+5;
        $add_time=strtotime($order_time." ".$S.":".$F.":".$M);  //提交时间
        $pay_time=strtotime($order_time." ".$S.":".$F.":".$MC);  //支付时间
        $confirm_time=strtotime($order_time." ".$SC.":".$F.":".$M);
        $shipping_time=strtotime($order_time." ".$SC.":".$F.":".$M);  //发货时间
        //收货时间
        $td=$d+rand(2,7);
        $take_time=strtotime($year.'-'.$mouth.'-'.$td." ".$S.":".$F.":".$M);
        $price=$val['sale_one_price'];
        $pay_name="支付宝支付";
        $goods_id_arr[]=$val['goods_id'];
        $ordersn_arr[]=$order_sn;
        $pay_order_arr[]=array(
            'goods_id'=>$val['goods_id'],
            'order_sn'=>$order_sn,
            'pay_time'=>$pay_time
        );
        $oi_sql .= "('".$order_sn."','".$user_row['user_id']."',".'1'.",".'2'.",".'2'.",'".$user_row['consignee']."','". $user_row['country']."','".$user_row['province']."','".$user_row['city']."','".$user_row['district']."','".$user_row['mobile']."',".'9'.",'".$pay_name."','".$price."','".$price."','".$price."','".$add_time."','".$confirm_time."','".$pay_time."','".$shipping_time."','".$take_time. "'),";
        $og_sql.="('".$user_row['user_id']."','".$goods_row['goods_id']."','".$goods_row['goods_name']."','".$goods_row['goods_sn']."','".$goods_row['market_price']."','".$goods_row['shop_price']."',".'1'.",".'2'.",".'24'."),";
    }
    $oi_sql = substr( $oi_sql,0, strlen($oi_sql)-1 );
    $og_sql = substr( $og_sql,0, strlen($og_sql)-1 );


    //拍拍活动添加
    $min_time=$year.'-'.$mouth.'-01'.' 00:00:00';
    $max_time=$year.'-'.$mouth.'-'.$days.' 23:59:59';
    $limit_min_time=strtotime($min_time);
    $limit_max_time=strtotime($max_time);

    for($p=0;$p<count($sale_data);$p++){
        $ppj_goods_id[]=$sale_data[$p]['goods_id'];
    }
    $ppj_goods_id_row=array_values(array_unique($ppj_goods_id));
    $ppj_add_sql="INSERT INTO ".$GLOBALS['ecs']->table('paipai_list')."(ppj_name,goods_id,ppj_no,goods_count,ppj_start_fee,ppj_buy_fee,start_time,end_time,ppj_margin_fee,ppj_startpay_time,ppj_endpay_time,ppj_createtime,goods_name,is_hot,is_new,review_status,user_id,ppj_staus) VALUES";
    for($pj=0;$pj<count($ppj_goods_id_row);$pj++){
        //拍拍活动数值
        $act_sql="SELECT ppj_no FROM ".$GLOBALS['ecs']->table('paipai_list')." WHERE goods_id=".$ppj_goods_id_row[$pj]." ORDER BY ppj_id DESC";
        $ppj_goods=$GLOBALS['db']->getRow($act_sql);

        $pg_sql='SELECT goods_id,goods_name,shop_price FROM '.$GLOBALS['ecs']->table('goods').' WHERE goods_id='.$ppj_goods_id_row[$pj];
        $goods_row2=$GLOBALS['db']->getRow($pg_sql);

        $ppj_no=$ppj_goods['ppj_no']+1;
        $goods_count=rand(50,200);
        $ppj_margin_fee="0.01";

        $goods_start_fee=str_replace(',','',number_format($goods_row2['shop_price']*0.70,2));

        $ppj_add_sql.="('".$goods_row2['goods_name']."','".$goods_row2['goods_id']."','".$ppj_no."','".$goods_count."','".$goods_start_fee."','".$goods_start_fee."','".$limit_min_time."','".$limit_max_time."','".$ppj_margin_fee."',".'20'.",".'20'.",'".$limit_min_time."','".$goods_row2['goods_name']."',".'0'.",".'0'.",".'3'.",".'0'.",".'2'."),";
    }
    $ppj_add_sql = substr( $ppj_add_sql,0, strlen($ppj_add_sql)-1 );

    $res=$GLOBALS['db']->query($oi_sql);
    if(!$res){
        var_dump('订单order_info添加失败'); exit;
    }
    $res2=$GLOBALS['db']->query($og_sql);
    if(!$res2){
        var_dump('订单order_goods添加失败'); exit;
    }
    $res3=$GLOBALS['db']->query($ppj_add_sql);
    if(!$res3){
        var_dump('拍拍活动添加失败'); exit;
    }

    //更改商品的库存数  以及order_info下的order_id
    $up_og_sql="UPDATE ".$GLOBALS['ecs']->table('order_goods')." SET  order_id=  CASE rec_id ";
    $up_g_sql="UPDATE ".$GLOBALS['ecs']->table('goods')." SET  goods_number=  CASE goods_id ";
    foreach($sale_data as $key2=>$val2){

        $one_paipai_sql="SELECT ppj_id,ppj_no,goods_id FROM ".$GLOBALS['ecs']->table('paipai_list')." WHERE goods_id=".$val2['goods_id']." AND start_time>=".$limit_min_time." AND end_time<=".$limit_max_time;
        $one_paipai_row=$GLOBALS['db']->getRow($one_paipai_sql);
        $paipai_row[]=array(
            'goods_id'=>$one_paipai_row['goods_id'],
            'ppj_id'=>$one_paipai_row['ppj_id'],
            'ppj_no'=>$one_paipai_row['ppj_no']
        );
        $order_id_sql="SELECT oi.order_id,oi.user_id,og.rec_id,og.goods_number FROM ".$GLOBALS['ecs']->table('order_goods')." AS og LEFT JOIN ".$GLOBALS['ecs']->table('order_info')." AS oi ON og.user_id=oi.user_id  WHERE goods_id=".$val2['goods_id']." AND goods_amount=".$val2['sale_one_price'];
        $order_id_row=$GLOBALS['db']->getRow($order_id_sql);
        if($order_id_row['order_id']){
            $up_og_sql.=" WHEN ".$order_id_row['rec_id'] ." THEN ".$order_id_row['order_id'];
            $rec_id_arr[]=$order_id_row['rec_id'];

            $upg_sql='SELECT goods_id,goods_number FROM '.$GLOBALS['ecs']->table('goods').' WHERE goods_id='.$val2['goods_id'];
            $upg_row=$GLOBALS['db']->getRow($upg_sql);
            $goods_id_count=implode(",", $goods_id_arr);
            $goods_one_display=substr_count($goods_id_count,$val2['goods_id']);
            $cut_number=$upg_row['goods_number']-$goods_one_display;
            if($upg_row['goods_number']<$goods_one_display){
                var_dump($upg_row['goods_id']."库存数量不足"); exit;
            }
            $up_g_sql.=" WHEN ".$val2['goods_id'] ." THEN ".$cut_number;

        }
    }
    $up_og_sql.=" END  WHERE rec_id in(".implode(",", $rec_id_arr).")";
    $up_g_sql.=" END  WHERE goods_id in(".implode(",", $goods_id_arr).")";
    $GLOBALS['db']->query($up_og_sql);
    $GLOBALS['db']->query($up_g_sql);


    //更改订单的ppj_id  ppj_no
    foreach($pay_order_arr as $key3=>$vo){
        $osn_sql='SELECT order_id FROM '.$GLOBALS['ecs']->table('order_info').' WHERE order_sn='.$vo['order_sn'];
        $osn_row=$GLOBALS['db']->getRow($osn_sql);
        foreach($paipai_row as $pkey=>$pvo){
            if($vo['goods_id']==$pvo['goods_id']){
                $pay_order_arr[$key3]['order_id']=$osn_row['order_id'];
                $pay_order_arr[$key3]['ppj_id']=$pvo['ppj_id'];
                $pay_order_arr[$key3]['ppj_no']=$pvo['ppj_no'];
            }
        }
    }

    $up_ogpp_sql="UPDATE ".$GLOBALS['ecs']->table('order_info')." SET  ppj_id=  CASE order_sn ";
    $up_gl_sql="UPDATE ".$GLOBALS['ecs']->table('goods_inventory_logs')." SET  order_id=  CASE goods_id ";
    foreach($pay_order_arr as $lkey=>$lval){
        $up_ogpp_sql.=" WHEN ".$lval['order_sn'] ." THEN ".$lval['ppj_id'];
        $up_gl_sql.=" WHEN ".$lval['goods_id'] ." THEN ".$lval['order_id'];
    }
    $up_ogpp_sql.=" END,ppj_no=  CASE order_sn ";
    $up_gl_sql.=" END,add_time=  CASE goods_id ";
    foreach($pay_order_arr as $lkey2=>$lval2){
        $up_ogpp_sql.=" WHEN ".$lval2['order_sn'] ." THEN ".$lval2['ppj_no'];
        $up_gl_sql.=" WHEN ".$lval2['order_sn'] ." THEN ".$lval2['pay_time'];
    }
    $up_ogpp_sql.=" END  WHERE order_sn in(".implode(",", $ordersn_arr).")";
    $up_gl_sql.=" END  WHERE goods_id in(".implode(",", $ppj_goods_id_row).") AND batch_number=".$batch_number;

    $GLOBALS['db']->query($up_ogpp_sql);
    $GLOBALS['db']->query($up_gl_sql);







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
//    $arr = array(
//        130,131,132,133,134,135,136,137,138,139,
//        144,147,
//        150,151,152,153,155,156,157,158,159,
//        176,177,178,
//        180,181,182,183,184,185,186,187,188,189,
//    );
//    for($i = 0; $i < 10; $i++) {
//        $tmp[] = $arr[array_rand($arr)].mt_rand(1000,9999).mt_rand(1000,9999);
//    }
//    $a=array();
//    foreach($tmp as $val){
//         $a[]['mobile']=$val;
//    }
//    foreach($a as $key=>$val){
//        $data[$key]['mobile']=$val['mobile'];
//        $data[$key]['email']=$val['mobile']."@163.com";
//        $data[$key]['nick_name']="PPJ".substr($val['mobile'],-6);;
//    }
//    foreach($data as $key=>$val){
//        $userdata=array('email'=>$val['email'],'user_name'=>$val['mobile'],'nick_name'=>$val['nick_name'],'password'=>'e10adc3949ba59abbe56e057f20f883e');
//        $aduser=$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $userdata, 'INSERT');
//    }
//    $zusql="SELECT user_id,user_name FROM ".$GLOBALS['ecs']->table('users');
//    $zu_data=$GLOBALS['db']->getRow($zusql);
//    $usql="SELECT user_id,user_name FROM ".$GLOBALS['ecs']->table('users')."WHERE user_id >".$zu_data['user_id']."-10";
//    $u_data=$GLOBALS['db']->getAll($usql);
//    foreach($u_data as $key2=>$uval){
//
//            $p=array(2,3,4,5,6,7,8,10,11,12,13,14,15,16,17,18,22,23,24,25,26,27,31,32);
//            $act_p=array_rand($p,1);
//
//            $psql="SELECT * FROM ".$GLOBALS['ecs']->table('region')." WHERE region_id=".$act_p." AND region_type='1' ";
//            $p_data=$GLOBALS['db']->getRow($psql);
//            //  echo "省:";var_dump($p_data['region_id']);var_dump($p_data['region_name']); echo "<br/>";
//            if($p_data['region_id']==''){
//                $p_data['region_id']=31;
//                $p_data['region_name']="浙江";
//            }
//            $csql="SELECT * FROM ".$GLOBALS['ecs']->table('region')." WHERE parent_id=".$p_data['region_id']." AND region_type='2' ";
//            $c_data=$GLOBALS['db']->getAll($csql);
//            $cact=array_rand($c_data,1);
//            $act_c=$c_data[$cact];
//            //  echo "市:";var_dump($act_c['region_id']);var_dump($act_c['region_name']); echo "<br/>";
//
//            $asql="SELECT * FROM ".$GLOBALS['ecs']->table('region')." WHERE parent_id=".$act_c['region_id']. " AND region_type='3' ";
//            $a_data=$GLOBALS['db']->getAll($asql);
//            $aact=array_rand($a_data,1);
//            $act_a=$a_data[$aact];
//            // echo "区/县:";var_dump($act_a['region_id']);var_dump($act_a['region_name']);
//            $consignee=generateName();
//
//            $adsdata=array(
//                'user_id'=>$uval['user_id'],
//                'mobile'=>$uval['user_name'],
//                'consignee'=>$consignee,
//                'country'=>'1',
//                'province'=>$p_data['region_id'],
//                'city'=>$act_c['region_id'],
//                'district'=>$act_a['region_id'],
//            );
//
//            $aduser=$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_address'), $adsdata, 'INSERT');
//    }

    //订单插入
    $year=2018;
    $mouth=4;
    if($mouth<10){
        $mouth='0'.$mouth;
    }
    $amount=2;

    //获取当前月份的天数
    $days =cal_days_in_month(CAL_GREGORIAN, $mouth, $year);
    $now_time=date('H:i:s',time());


    var_dump(date("H:i:s",time()+8*3600));

    $oi_sql = "INSERT INTO ".$GLOBALS['ecs']->table('order_info')." (order_sn,user_id,order_status,shipping_status,pay_status,consignee,country,province,city,district,mobile,pay_id,pay_name,goods_amount,money_paid,order_amount,add_time,confirm_time,pay_time,shipping_time,confirm_take_time) VALUES ";
    $og_sql="INSERT INTO ".$GLOBALS['ecs']->table('order_goods')."(user_id,goods_id,goods_name,goods_sn,market_price,goods_price,is_real,warehouse_id,area_id,money_paid) VALUES";
    for($d=1;$d<=$days;$d++){
        if($d<10){
            $d='0'.$d;
        }
        //数据写入
        for($i=1;$i<=$amount;$i++){
            //随机一个用户
            $user_id=rand(40079,861288);
            $u_sql='SELECT u.user_id,ua.consignee,ua.country,ua.province,ua.city,ua.district,ua.mobile FROM '.$GLOBALS['ecs']->table('users').' AS u LEFT JOIN '.$GLOBALS['ecs']->table('user_address').'AS ua ON u.user_id=ua.user_id  WHERE u.user_id='.$user_id;
            $user_row=$GLOBALS['db']->getRow($u_sql);
//            if(!$user_row['consignee']){
//                $user_id=32527;
//                $u_sql='SELECT u.user_id,ua.consignee,ua.country,ua.province,ua.city,ua.district,ua.mobile FROM '.$GLOBALS['ecs']->table('users').' AS u LEFT JOIN '.$GLOBALS['ecs']->table('user_address').'AS ua ON u.user_id=ua.user_id  WHERE u.user_id='.$user_id;
//                $user_row=$GLOBALS['db']->getRow($u_sql);
//            }
            //随机一件商品
            $gid_sql='select goods_id FROM '.$GLOBALS['ecs']->table('goods');
            $gid_row=$GLOBALS['db']->getAll($gid_sql);

            $goods_rand_id=array_rand($gid_row,1);
            $goods_id=$gid_row[$goods_rand_id]['goods_id'];
            $g_sql2='SELECT goods_id,goods_name,goods_sn,market_price,shop_price FROM '.$GLOBALS['ecs']->table('goods').' WHERE goods_id='.$goods_id;
            $goods_row=$GLOBALS['db']->getRow($g_sql2);
            $price=str_replace(',','',number_format(rand($goods_row['shop_price']*0.75,$goods_row['shop_price']*0.95),2));

            $order_sn=$year.$mouth.$d.str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
            $order_time=$year.'-'.$mouth.'-'.$d;
            $S=rand(8,22);//随机--时
            $F=rand(0,59);//随机--分
            $M=rand(0,50);//随机--秒
            $SC=$S+1;
            $MC=$M+5;
            $add_time=strtotime($order_time." ".$S.":".$F.":".$M);  //提交时间
            $pay_time=strtotime($order_time." ".$S.":".$F.":".$MC);  //支付时间
            $confirm_time=strtotime($order_time." ".$SC.":".$F.":".$M);
            $shipping_time=strtotime($order_time." ".$SC.":".$F.":".$M);  //发货时间
            //收货时间
            $td=$d+rand(2,7);
            $take_time=strtotime($year.'-'.$mouth.'-'.$td." ".$S.":".$F.":".$M);

            $pay_name="支付宝支付";

            $oi_sql .= "('".$order_sn."','".$user_row['user_id']."',".'1'.",".'2'.",".'2'.",'".$user_row['consignee']."','". $user_row['country']."','".$user_row['province']."','".$user_row['city']."','".$user_row['district']."','".$user_row['mobile']."',".'9'.",'".$pay_name."','".$price."','".$price."','".$price."','".$add_time."','".$confirm_time."','".$add_time."','".$shipping_time."','".$take_time. "'),";
            $og_sql.="('".$user_row['user_id']."','".$goods_row['goods_id']."','".$goods_row['goods_name']."','".$goods_row['goods_sn']."','".$goods_row['market_price']."','".$goods_row['shop_price']."',".'1'.",".'2'.",".'24'.",'".$price."'),";

        }
    }

    $oi_sql = substr( $oi_sql,0, strlen($oi_sql)-1 );
    $og_sql = substr( $og_sql,0, strlen($og_sql)-1 );

    $res=$GLOBALS['db']->query($oi_sql);
    $res2=$GLOBALS['db']->query($og_sql);

    $min_time=$year.'-'.$mouth.'-01'.' 00:00:00';
    $max_time=$year.'-'.$mouth.'-'.$days.' 23:59:59';

    $limit_min_time=strtotime($min_time);
    $limit_max_time=strtotime($max_time);

    //更新order_goods下order_id
    $sql3="SELECT order_id,user_id,goods_amount FROM ".$GLOBALS['ecs']->table('order_info')." WHERE add_time>".$limit_min_time." AND add_time<".$limit_max_time;
    $order_res=$GLOBALS['db']->getAll($sql3);
    foreach($order_res as $key2=>$val2){
          $sql4="UPDATE ".$GLOBALS['ecs']->table('order_goods')." SET order_id=".$val2['order_id']." WHERE  user_id=".$val2['user_id']." AND money_paid=".$val2['goods_amount'];
          $GLOBALS['db']->query($sql4);
    }

    //活动自动添加
    $pp_sql="SELECT distinct(og.goods_id) FROM ".$GLOBALS['ecs']->table('order_goods')."AS og LEFT JOIN ".$GLOBALS['ecs']->table('order_info')." AS oi ON og.order_id=oi.order_id WHERE oi.add_time>".$limit_min_time." AND oi.add_time<".$limit_max_time;
    $order_goods=$GLOBALS['db']->getAll($pp_sql);
    $ppj_add_sql="INSERT INTO ".$GLOBALS['ecs']->table('paipai_list')."(ppj_name,goods_id,ppj_no,goods_count,ppj_start_fee,ppj_buy_fee,start_time,end_time,ppj_margin_fee,ppj_startpay_time,ppj_endpay_time,ppj_createtime,goods_name,is_hot,is_new,review_status,user_id,ppj_staus) VALUES";
    foreach($order_goods as $key3 => $val3){
        $act_sql="SELECT g.goods_id,g.goods_name,g.shop_price,pl.ppj_no FROM ".$GLOBALS['ecs']->table('goods')." AS g LEFT JOIN ".$GLOBALS['ecs']->table('paipai_list')." AS pl ON g.goods_id=pl.goods_id WHERE g.goods_id=".$val3['goods_id']." ORDER BY pl.ppj_id DESC";
        $ppj_goods=$GLOBALS['db']->getRow($act_sql);
        $ppj_no=$ppj_goods['ppj_no']+1;
        $goods_count=rand(50,200);
        $ppj_margin_fee="0.01";
        $goods_start_fee=str_replace(',','',number_format($ppj_goods['shop_price']*0.70,2));
        $ppj_add_sql.="('".$ppj_goods['goods_name']."','".$ppj_goods['goods_id']."','".$ppj_no."','".$goods_count."','".$goods_start_fee."','".$goods_start_fee."','".$limit_min_time."','".$limit_max_time."','".$ppj_margin_fee."',".'20'.",".'20'.",'".$limit_min_time."','".$ppj_goods['goods_name']."',".'0'.",".'0'.",".'3'.",".'0'.",".'2'."),";
    }
    $ppj_add_sql = substr( $ppj_add_sql,0, strlen($ppj_add_sql)-1 );
//    var_dump($ppj_add_sql);
    $ppj_res=$GLOBALS['db']->query($ppj_add_sql);

    var_dump(date("H:i:s",time()+8*3600));

    $smarty->display('paipai_part_info.dwt');


}
elseif($_REQUEST['act'] == 'order'){


    $cat_sql="SELECT cat_id,cat_name FROM ".$GLOBALS['ecs']->table('category')." WHERE parent_id=0 AND is_show=1";
    $cat_row=$GLOBALS['db']->getAll($cat_sql);
    $smarty->assign('cat_row', $cat_row);

    $mouth=$_POST['mouth'];
    $days=$_POST['days'];
    $stoprice=$_POST['storage_price'];
    $outprice=$_POST['out_price'];
    $cat_id=$_POST['cat_id'];


    $year=2018;
    $order_time=$year.'-'.$mouth.'-'.$days;
    $S=rand(8,18);//随机--时
    $F=rand(0,59);//随机--分
    $M=rand(0,59);//随机--秒
    $add_time=strtotime($order_time." ".$S.":".$F.":".$M);
    if($cat_id && $stoprice && $mouth && $days) {
        var_dump(date("H:i:s",time()+8*3600));
        $cat2_sql = "SELECT cat_id,cat_name,parent_id FROM " . $GLOBALS['ecs']->table('category') . " WHERE parent_id=" . $cat_id." AND is_show=1 ";
        $cat2_row = $GLOBALS['db']->getAll($cat2_sql);

        foreach ($cat2_row as $key => $catval) {
            $goods_sql = "SELECT count(goods_id) as count FROM " . $GLOBALS['ecs']->table('goods') . " WHERE cat_id=" . $catval['cat_id'];
            $goods_count[] = $GLOBALS['db']->getAll($goods_sql);
            $goods_sql2 = "SELECT goods_id,cat_id,cost_price FROM " . $GLOBALS['ecs']->table('goods') . " WHERE cat_id=" . $catval['cat_id']." ORDER BY shop_price DESC";
            $goods_row[] = $GLOBALS['db']->getAll($goods_sql2);
        }
        if($goods_count[0][0]['count'] == '0'){
             var_dump("商品数量为空"); exit;
        }
        for ($i = 0; $i < count($goods_count); $i++) {
            $goods_sum += $goods_count[$i][0]['count'];
        }
        $goods_count_price = grouping($stoprice, $goods_sum, 1000);
        for ($i = 0; $i < count($goods_row); $i++) {
            $new_goods = $goods_row[$i];
            for ($y = 0; $y < count($new_goods); $y++) {
                $new2_goods[] = $new_goods[$y];
            }
        }
        for ($g = 0; $g < count($goods_count_price); $g++) {
            $goods_data[] = array(
                'goods_id' => $new2_goods[$g]['goods_id'],
                'cost_price' => $new2_goods[$g]['cost_price'],
                'total_price' => $goods_count_price[$g + 1]
            );
        }

        $goods_logs_sql = "INSERT INTO " . $GLOBALS['ecs']->table('goods_inventory_logs') . "(goods_id,use_storage,admin_id,number,product_id,add_time) VALUES ";
        foreach ($goods_data as $key4 => $val4) {
            $sel_sql = "SELECT goods_number FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id=" . $val4['goods_id'];
            $old_goods_num = $GLOBALS['db']->getRow($sel_sql);
            if ($old_goods_num) {
                $goods_number = round($val4['total_price'] / $val4['cost_price']) + $old_goods_num['goods_number'];
            } else {
                $goods_number = round($val4['total_price'] / $val4['cost_price']);
            }
          $upd_sql="UPDATE ".$GLOBALS['ecs']->table('goods')." SET goods_number=".$goods_number." WHERE goods_id=".$val4['goods_id'];
          $GLOBALS['db']->query($upd_sql);

          $goods_logs_sql .= "( '" . $val4['goods_id'] . "'," . '7' . "," . '59' . ",'" . $goods_number . "'," . '0' . ",'" . $add_time ."'),";
        }
        $goods_logs_sql = substr($goods_logs_sql, 0, strlen($goods_logs_sql) - 1);
        $GLOBALS['db']->query($goods_logs_sql);
        var_dump(date("H:i:s",time()+8*3600));
    }else{
        var_dump("q请填写有效数据");
    }

    $smarty->display('paipai_part_order.dwt');
}
elseif($_REQUEST['act'] == 'outorder') {

    $cat_sql = "SELECT cat_id,cat_name FROM " . $GLOBALS['ecs']->table('category') . " WHERE parent_id=0 AND is_show=1";
    $cat_row = $GLOBALS['db']->getAll($cat_sql);
    $smarty->assign('cat_row', $cat_row);

    $mouth = $_POST['mouth'];
    $days = $_POST['days'];
    $outprice = $_POST['out_price'];
    $saleprice = $_POST['sale_price'];
    $cat_id = $_POST['cat_id'];


    if ($cat_id && $outprice && $mouth) {
        var_dump(date("H:i:s", time() + 8 * 3600));
        $cat2_sql = "SELECT cat_id,cat_name,parent_id FROM " . $GLOBALS['ecs']->table('category') . " WHERE parent_id=" . $cat_id . " AND is_show=1 ";
        $cat2_row = $GLOBALS['db']->getAll($cat2_sql);
        foreach ($cat2_row as $key => $catval) {
            $goods_sql = "SELECT count(goods_id) as count FROM " . $GLOBALS['ecs']->table('goods') . " WHERE cat_id=" . $catval['cat_id'];
            $goods_count[] = $GLOBALS['db']->getAll($goods_sql);
            $goods_sql2 = "SELECT goods_id,cat_id,cost_price,shop_price FROM " . $GLOBALS['ecs']->table('goods') . " WHERE cat_id=" . $catval['cat_id'] . " ORDER BY shop_price DESC";
            $goods_row[] = $GLOBALS['db']->getAll($goods_sql2);
        }
        if ($goods_count[0][0]['count'] == '0') {
            var_dump("商品数量为空");
            exit;
        }
        for ($i = 0; $i < count($goods_count); $i++) {
            $goods_sum += $goods_count[$i][0]['count'];
        }
        $goods_count_price = grouping($outprice, $goods_sum, 1000);
        for ($i = 0; $i < count($goods_row); $i++) {
            $new_goods = $goods_row[$i];
            for ($y = 0; $y < count($new_goods); $y++) {
                $new2_goods[] = $new_goods[$y];
            }
        }
        for ($g = 0; $g < count($goods_count_price); $g++) {
            $goods_data[] = array(
                'goods_id' => $new2_goods[$g]['goods_id'],
                'cost_price' => $new2_goods[$g]['cost_price'],
                'shop_price' => $new2_goods[$g]['shop_price'],
                'total_price' => $goods_count_price[$g + 1],
                'out_num' => round($goods_count_price[$g + 1] / $new2_goods[$g]['cost_price'])
            );
        }
        //出库添加
        $batch_number=time();
        $out_logs_sql = "INSERT INTO ".$GLOBALS['ecs']->table('goods_inventory_logs')."(goods_id,use_storage,admin_id,number,batch_number) VALUES ";
        foreach ($goods_data as $key => $val) {
            for ($i = 1; $i <= $val['out_num']; $i++) {
                $out_logs_sql .= "( '".$val['goods_id']."',".'7'.",".'59'.",".'-1'.",'".$batch_number."'),";
            }
        }
        $out_logs_sql = substr( $out_logs_sql,0, strlen($out_logs_sql)-1 );
        $GLOBALS['db']->query($out_logs_sql);
        $goods_sale_num = grouping($saleprice, $goods_sum, 10000);
        foreach ($goods_data as $gdkey => $gdval){
            for($gi=1;$gi<=$gdval['out_num'];$gi++){
                $one_price = str_replace(',', '', number_format($goods_sale_num[$s + 1] / $gdval['out_num'], 2));
                $sale_data[] = array(
                    'goods_id' => $gdval['goods_id'],
                    'cost_price' => $gdval['cost_price'],
                    'shop_price' => $gdval['shop_price'],
                    'total_price' => $gdval['total_price'],
                    'out_num' => $gdval['out_num'],
                    'sale_total_price' => $goods_sale_num[$s + 1],
                    'sale_one_price' => str_replace(',', '', number_format(rand($one_price * 0.90, $one_price * 1.10), 2))
                );
            }
        }

        $row = order_add($sale_data, $mouth,$batch_number);
//        var_dump($row);
        
        var_dump(date("H:i:s", time() + 8 * 3600));
    } else {
        var_dump("q请填写有效数据");
    }

    $smarty->display('paipai_part_outorder.dwt');
}