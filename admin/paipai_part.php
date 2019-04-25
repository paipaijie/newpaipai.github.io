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
function delivery_sn()
{
    mt_srand((double) microtime() * 1000000);
    return '8'.date('dHi') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
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
function ordertime($year,$mouth,$days){
    $d=rand(1,$days);
    if($mouth<10){
        $mouth='0'.$mouth;
    }
    if($d<10){
        $d='0'.$d;
    }
    $order_time = $year . '-' . $mouth . '-' . $d;
    $S = rand(8, 22);//随机--时
    if ($S < 10) {
        $S = '0' . $S;
    }
    $F = rand(0, 59);//随机--分
    if ($F < 10) {
        $F = '0' . $F;
    }
    $M = rand(0, 50);//随机--秒
    if ($M < 10) {
        $M = '0' . $M;
    }
    $SC = $S + 1;
    $MC = $M + 5;
    $order_date['order_time']=$year . $mouth .$d;
    $order_date['add_time'] = strtotime($order_time . " " . $S . ":" . $F . ":" . $M);  //提交时间
    $order_date['pay_time'] = strtotime($order_time . " " . $S . ":" . $F . ":" . $MC);  //支付时间
    $order_date['confirm_time'] = strtotime($order_time . " " . $SC . ":" . $F . ":" . $M);
    $order_date['shipping_time'] = strtotime($order_time . " " . $SC . ":" . $F . ":" . $M);  //发货时间
    //收货时间
    $td = $days + rand(2, 7);
    $order_date['take_time'] = strtotime($year . '-' . $mouth . '-' . $td . " " . $S . ":" . $F . ":" . $M);
    return $order_date;
}
function order_add($sale_data,$year, $mouth,$ceil_order_num){

    $batch_number=time();
    $days =cal_days_in_month(CAL_GREGORIAN, $mouth, $year);
    $oi_sql = "INSERT INTO ".$GLOBALS['ecs']->table('order_info')." (order_sn,user_id,order_status,shipping_status,pay_status,consignee,country,province,city,district,mobile,pay_id,pay_name,goods_amount,money_paid,order_amount,add_time,confirm_time,pay_time,shipping_time,confirm_take_time) VALUE ";
    $og_sql="INSERT INTO ".$GLOBALS['ecs']->table('order_goods')."(user_id,goods_id,goods_name,goods_sn,market_price,goods_price,is_real,warehouse_id,area_id,order_sn) VALUES";
    $out_logs_sql = "INSERT INTO ".$GLOBALS['ecs']->table('goods_inventory_logs')."(goods_id,use_storage,admin_id,number,add_time,batch_number,order_sn) VALUES ";

    foreach($sale_data as $key=>$val){

        $d=rand(1,$days);
        if($d<10){
            $d='0'.$days;
        }
        $order_sn=$year.$mouth.$d.str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);

        $price=$val['sale_one_price'];
        $pay_name="支付宝支付";
        $goods_id_arr[]=$val['goods_id'];
        $ordersn_arr[]=$order_sn;
        $sale_data[$key]['order_sn']=$order_sn;

        $oi_sql .= "('".$order_sn."','".$val['user_id']."',".'1'.",".'2'.",".'2'.",'".$val['consignee']."','". $val['country']."','".$val['province']."','".$val['city']."','".$val['district']."','".$val['mobile']."',".'9'.",'".$pay_name."','".$price."','".$price."','".$price."','".$val['add_time']."','".$val['confirm_time']."','".$val['pay_time']."','".$val['shipping_time']."','".$val['take_time']. "'),";
        $og_sql.="('".$val['user_id']."','".$val['goods_id']."','".$val['goods_name']."','".$order_sn."','".$val['market_price']."','".$val['shop_price']."',".'1'.",".'2'.",".'24'.",'".$order_sn."'),";
        $out_logs_sql .= "( '".$val['goods_id']."',".'8'.",".'59'.",".'-1'.",'".$val['pay_time']."','".$batch_number."','".$order_sn."'),";

    }
    $oi_sql = substr( $oi_sql,0, strlen($oi_sql)-1 );
    $og_sql = substr( $og_sql,0, strlen($og_sql)-1 );
    $out_logs_sql = substr( $out_logs_sql,0, strlen($out_logs_sql)-1 );
    $res1=$GLOBALS['db']->query($oi_sql);
    if($res1){
       $res2=$GLOBALS['db']->query($og_sql);
       if($res2){
           $res3=$GLOBALS['db']->query($out_logs_sql);
       }
    }

    if(count($sale_data)%5000==0){
        $GLOBALS['db']->query('commit');
        $GLOBALS['db']->query('begin');
    }
    $GLOBALS['db']->query('commit');

    //拍拍活动添加
    $min_time=$year.'-'.$mouth.'-01'.' 00:00:00';
    $max_time=$year.'-'.$mouth.'-'.$days.' 23:59:59';
    $limit_min_time=strtotime($min_time);
    $limit_max_time=strtotime($max_time);

    for($p=0;$p<count($sale_data);$p++){
        $ppj_goods_id[]=$sale_data[$p]['goods_id'];
    }
    $ppj_goods_id_row=array_values(array_unique($ppj_goods_id));

    //更改商品的库存数
//    $up_g_sql="UPDATE ".$GLOBALS['ecs']->table('goods')." SET  goods_number=  CASE goods_id ";
//    $goods_id_count=implode(",", $goods_id_arr);
//    for($ug=0;$ug<count($ppj_goods_id_row);$ug++){
//
//        $up_g_sql.=" WHEN ".$ppj_goods_id_row[$ug] ." THEN ".$sale_data[$ug]['goods_number'];
//    }
//    $up_g_sql.=" END  WHERE goods_id in(".implode(",", $ppj_goods_id_row).")";
//
//    $GLOBALS['db']->query($up_g_sql);


    $ppj_add_sql="INSERT INTO ".$GLOBALS['ecs']->table('paipai_list')."(ppj_name,goods_id,ppj_no,goods_count,ppj_start_fee,ppj_buy_fee,start_time,end_time,ppj_margin_fee,ppj_startpay_time,ppj_endpay_time,ppj_createtime,goods_name,is_hot,is_new,review_status,user_id,ppj_staus) VALUES";
    foreach($ppj_goods_id_row as $pkey){
        //最近一期商品的拍拍活动
        $act_sql="SELECT ppj_no FROM ".$GLOBALS['ecs']->table('paipai_list')." WHERE goods_id=".$pkey." ORDER BY ppj_id DESC";
        $ppj_goods=$GLOBALS['db']->getRow($act_sql);

        $pg_sql='SELECT goods_id,goods_name,shop_price FROM '.$GLOBALS['ecs']->table('goods').' WHERE goods_id='.$pkey;
        $goods_row2=$GLOBALS['db']->getRow($pg_sql);

        $ppj_no=$ppj_goods['ppj_no']+1;
        $goods_count=ceil(rand($ceil_order_num*1.3,$ceil_order_num*2));
        $ppj_margin_fee="0.00";

        $goods_start_fee=str_replace(',','',number_format($goods_row2['shop_price']*0.70,2));
        $ppj_add_sql.="('".$goods_row2['goods_name']."','".$goods_row2['goods_id']."','".$ppj_no."','".$goods_count."','".$goods_start_fee."','".$goods_start_fee."','".$limit_min_time."','".$limit_max_time."','".$ppj_margin_fee."',".'20'.",".'20'.",'".$limit_min_time."','".$goods_row2['goods_name']."',".'0'.",".'0'.",".'3'.",".'0'.",".'2'."),";
    }
    $ppj_add_sql = substr( $ppj_add_sql,0, strlen($ppj_add_sql)-1 );
 
    if($res3){
        $res4=$GLOBALS['db']->query($ppj_add_sql);
        if($res4){
            return '1';
        }
    }



}
function paipai_order($sale_data){

    $oi_sql = "INSERT INTO ".$GLOBALS['ecs']->table('order_info')." (order_sn,user_id,order_status,shipping_status,pay_status,consignee,country,province,city,district,mobile,pay_id,pay_name,goods_amount,money_paid,order_amount,add_time,confirm_time,pay_time,shipping_time,confirm_take_time) VALUE ";
    $og_sql="INSERT INTO ".$GLOBALS['ecs']->table('order_goods')."(user_id,goods_id,goods_name,goods_sn,market_price,goods_price,is_real,warehouse_id,area_id,order_sn) VALUES";
    $out_logs_sql = "INSERT INTO ".$GLOBALS['ecs']->table('goods_inventory_logs')."(goods_id,use_storage,admin_id,number,add_time,batch_number,order_sn) VALUES ";
    $batch_number=time();
    foreach($sale_data as $key=>$val){

        $price=$val['sale_one_price'];
        $pay_name="支付宝支付";
        $goods_id_arr[]=$val['goods_id'];
        $ordersn_arr[]=$val['order_sn'];
        $sale_data[$key]['order_sn']=$val['order_sn'];

        $oi_sql .= "('".$val['order_sn']."','".$val['user_id']."',".'1'.",".'2'.",".'2'.",'".$val['consignee']."','". $val['country']."','".$val['province']."','".$val['city']."','".$val['district']."','".$val['mobile']."',".'9'.",'".$pay_name."','".$price."','".$price."','".$price."','".$val['add_time']."','".$val['confirm_time']."','".$val['pay_time']."','".$val['shipping_time']."','".$val['take_time']. "'),";
        $og_sql.="('".$val['user_id']."','".$val['goods_id']."','".$val['goods_name']."','".$val['goods_sn']."','".$val['market_price']."','".$val['shop_price']."',".'1'.",".'2'.",".'24'.",'".$val['order_sn']."'),";
        $out_logs_sql .= "( '".$val['goods_id']."',".'8'.",".'59'.",".'1'.",'".$val['pay_time']."','".$batch_number."','".$val['order_sn']."'),";

    }
    $oi_sql = substr( $oi_sql,0, strlen($oi_sql)-1 );
    $og_sql = substr( $og_sql,0, strlen($og_sql)-1 );
    $out_logs_sql = substr( $out_logs_sql,0, strlen($out_logs_sql)-1 );
    $res1=$GLOBALS['db']->query($oi_sql);
    if($res1){
        $res2=$GLOBALS['db']->query($og_sql);
        if($res2){
            $res3=$GLOBALS['db']->query($out_logs_sql);
        }
    }

    if(count($sale_data)%5000==0){
        $GLOBALS['db']->query('commit');
        $GLOBALS['db']->query('begin');
    }
    $res4=$GLOBALS['db']->query('commit');
    return $res4;
//    for($p=0;$p<count($sale_data);$p++){
//        $ppj_goods_id[]=$sale_data[$p]['goods_id'];
//    }
//    $ppj_goods_id_row=array_values(array_unique($ppj_goods_id));
//
//    //更改商品的库存数
//    $up_g_sql="UPDATE ".$GLOBALS['ecs']->table('goods')." SET  goods_number=  CASE goods_id ";
//    $goods_id_count=implode(",", $goods_id_arr);
//    for($ug=0;$ug<count($ppj_goods_id_row);$ug++){
//
//        $up_g_sql.=" WHEN ".$ppj_goods_id_row[$ug] ." THEN ".$sale_data[$ug]['goods_number'];
//    }
//    $up_g_sql.=" END  WHERE goods_id in(".implode(",", $ppj_goods_id_row).")";
//
//    $GLOBALS['db']->query($up_g_sql);
    //更改paipai_list订单数



}
function ppj_auto($ppj_row,$year,$mouth,$days){

    $time=date('H:i:s',time()+8*3600);
    $now_time=strtotime($year.'-'.$mouth.'-'.$days.' '.$time);
    //订单保证金添加
    $m_oi_sql = "INSERT INTO ".$GLOBALS['ecs']->table('order_info')." (order_sn,user_id,order_status,shipping_status,pay_status,consignee,country,province,city,district,mobile,pay_id,pay_name,goods_amount,money_paid,order_amount,referer,add_time,pay_time,extension_code,extension_id,ppj_id,ppj_no) VALUE ";
    $m_og_sql="INSERT INTO ".$GLOBALS['ecs']->table('order_goods')."(user_id,goods_id,goods_name,goods_sn,market_price,goods_price,is_real,warehouse_id,area_id,ppj_no,order_sn) VALUES";
    //保证金记录添加
    $ppj_pm_sql = "INSERT INTO ".$GLOBALS['ecs']->table('paipai_seller_pay_margin')."(user_id,ppj_id,ppj_no,order_sn,pay_fee,ls_pay_ok,ls_refund,createtime,paytime) VALUES ";
    foreach($ppj_row as $moikey=>$moival){
        $sel_g_sql ="SELECT goods_id,goods_name,goods_sn,cost_price,shop_price,market_price FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id=".$moival['goods_id'];
        $moi_goods_row=$GLOBALS['db']->getRow($sel_g_sql);
        $mu_sql='SELECT u.user_id,ua.consignee,ua.country,ua.province,ua.city,ua.district,ua.mobile FROM '.$GLOBALS['ecs']->table('users').' AS u LEFT JOIN '.$GLOBALS['ecs']->table('user_address').'AS ua ON u.user_id=ua.user_id  WHERE u.user_id='.$moival['user_id'];
        $moi_user_row=$GLOBALS['db']->getRow($mu_sql);
        $moi_order_sn=$year.$mouth.$days.str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

        $moiprice='0.00';
        $pay_name="在线支付";
        $moi_ordersn_arr[]=$moi_order_sn;
        $extension_code="paipai_buy";
        $referer='touch';

        $m_oi_sql .= "('".$moi_order_sn."','".$moival['user_id']."',".'1'.",".'0'.",".'10'.",'".$moi_user_row['consignee']."','". $moi_user_row['country']."','".$moi_user_row['province']."','".$moi_user_row['city']."','".$moi_user_row['district']."','".$moi_user_row['mobile']."',".'15'.",'".$pay_name."','".$moiprice."','".$moiprice."','".$moiprice."','".$referer."','".$now_time."','".$now_time."','".$extension_code."','".$moival['goods_id']."','".$moival['ppj_id']."','".$moival['ppj_no']."'),";
        $m_og_sql.="('".$moival['user_id']."','".$moival['goods_id']."','".$moi_goods_row['goods_name']."','".$moi_order_sn."','".$moi_goods_row['market_price']."','".$moi_goods_row['shop_price']."',".'1'.",".'2'.",".'24'.",'".$moival['ppj_no']."','".$moi_order_sn."'),";
        $ppj_pm_sql .= "( '".$moival['user_id']."','".$moival['ppj_id']."','".$moival['ppj_no']."','".$moi_order_sn."',".'0.00'.",".'1'.",".'0'.",'".$now_time."','".$now_time."'),";
    }
    $m_oi_sql = substr( $m_oi_sql,0, strlen($m_oi_sql)-1 );
    $m_og_sql = substr( $m_og_sql,0, strlen($m_og_sql)-1 );
    $ppj_pm_sql = substr( $ppj_pm_sql,0, strlen($ppj_pm_sql)-1 );

    $m_oi_int=$GLOBALS['db']->query($m_oi_sql);
    if($m_oi_int){
        $m_og_int=$GLOBALS['db']->query($m_og_sql);
        if($m_og_int){
            $pm_int=$GLOBALS['db']->query($ppj_pm_sql);
            if($pm_int){
                $m_order_id_sql = "SELECT order_id,order_sn FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_sn IN (".implode(",", $moi_ordersn_arr).") ORDER BY order_id DESC ";
                $m_update_data=$GLOBALS['db']->getAll($m_order_id_sql);
                $m_rec_id_sql = "SELECT rec_id,order_sn,goods_id FROM " . $GLOBALS['ecs']->table('order_goods') . " WHERE order_sn IN (".implode(",", $moi_ordersn_arr).") ORDER BY rec_id DESC ";
                $m_rec_id_row = $GLOBALS['db']->getALL($m_rec_id_sql);
                $pm_id_sql = "SELECT spm_id,order_sn FROM " . $GLOBALS['ecs']->table('paipai_seller_pay_margin') . " WHERE order_sn IN (".implode(",", $moi_ordersn_arr).") ORDER BY spm_id DESC ";
                $pm_id_row = $GLOBALS['db']->getALL($pm_id_sql);
                foreach($m_update_data as $mkey=>$mval) {
                    if($mval['order_sn']==$m_rec_id_row[$mkey]['order_sn']){
                        $m_update_data[$mkey]['rec_id']=$m_rec_id_row[$mkey]['rec_id'];
                        $m_rec_id_arr[] = $m_rec_id_row[$mkey]['rec_id'];
                    }
                    if($mval['order_sn']==$pm_id_row[$mkey]['order_sn']){
                        $m_update_data[$mkey]['spm_id']=$pm_id_row[$mkey]['spm_id'];
                        $pm_id_arr[] = $pm_id_row[$mkey]['spm_id'];
                    }
                }
                //保证金数据
                //更改order_info下的order_id   更改paipai_seller_pay_margin下的order_id
                $m_up_og_sql="UPDATE  ".$GLOBALS['ecs']->table('order_goods')." SET  order_id= CASE rec_id";
                $m_up_pm_sql="UPDATE  ".$GLOBALS['ecs']->table('paipai_seller_pay_margin')." SET  order_id= CASE spm_id";
                foreach($m_update_data as $mkey2=>$mval2) {
                    $m_up_og_sql.=" WHEN ".$mval2['rec_id']." THEN ". $mval2['order_id'];
                    $m_up_pm_sql.=" WHEN ".$mval2['spm_id']." THEN ". $mval2['order_id'];
                }
                $m_up_og_sql.=" END WHERE rec_id IN(".implode(",", $m_rec_id_arr).") ";
                $m_up_pm_sql.=" END WHERE spm_id IN(".implode(",", $pm_id_arr).") ";
                $m_uog=$GLOBALS['db']->query($m_up_og_sql);
                if($m_uog){
                    $m_upm=$GLOBALS['db']->query($m_up_pm_sql);
                    if(!$m_upm){
                        var_dump(更改paipai_seller_pay_margin保证金的order_id失败); exit;
                    }
                }else{
                    var_dump(更改order_goods的order_id记录失败); exit;
                }
            }
        }
    }


    //出价记录添加
    $ppj_bid_sql = "INSERT INTO ".$GLOBALS['ecs']->table('paipai_goods_bid_user')."(user_id,ppj_id,ppj_no,bid_price,bid_time,is_status,createtime,rt_auto,spm_id) VALUES ";
    foreach($ppj_row as $key2=>$val2){
        $sel_pm_sql ="SELECT spm_id FROM ".$GLOBALS['ecs']->table('paipai_seller_pay_margin')." WHERE user_id=".$val2['user_id']." AND ppj_id=".$val2['ppj_id']." ORDER BY spm_id DESC";;
        $margin_row=$GLOBALS['db']->getRow($sel_pm_sql);
        $sel_bu_sql ="SELECT bid_price,count(bid_id) as total_bid FROM ".$GLOBALS['ecs']->table('paipai_goods_bid_user')." WHERE ppj_id=".$val2['ppj_id']." ORDER BY bid_id DESC";
        $bid_row=$GLOBALS['db']->getRow($sel_bu_sql);
        //获取当前价
        $price_ladder = $val2['ext_info']['price_ladder'];
        $cur_amount=$bid_row['total_bid'];
        foreach ($price_ladder as $plkey => $amount_price) {
            if ($amount_price['amount'] <= $cur_amount) {
                $cur_price = $amount_price['price'];
            }
            else if( $cur_amount == 0 ) {
                $cur_price=$amount_price['price'];
                break;
            }
        }
        $mag_arr=array(0.04,0.05,0.06,0.07,0.08);
        $ceil_around=array_rand($mag_arr,1);
        $pay_price=$cur_price+$cur_price*$mag_arr[$ceil_around];
        if($pay_price>=1000){
            $pay_price=str_replace(',','',number_format($pay_price,2));
        }else{
            $pay_price=number_format($pay_price,2);
        }

        $min_time=$year.'-'.$mouth.'-'.$days.' 07:00:00';
        $max_time=$year.'-'.$mouth.'-'.$days.' 22:00:00';
        $limit_min_time=strtotime($min_time);
        $limit_max_time=strtotime($max_time);
        if($now_time >= $limit_min_time && $now_time<=$limit_max_time){
            $bid_status=2;
        }else{
            $bid_status=0;
        }
        $ppj_row[$key2]['pay_price']=$pay_price;
        $rt_auto='1';
        $ppj_row[$key2]['spm_id']=$margin_row['spm_id'];
        $ppj_bid_sql .= "( '".$val2['user_id']."','".$val2['ppj_id']."','".$val2['ppj_no']."','".$pay_price."','".$now_time."',".$bid_status.",'".$now_time."','".$rt_auto."','".$margin_row['spm_id']."'),";
    }
    $ppj_bid_sql = substr( $ppj_bid_sql,0, strlen($ppj_bid_sql)-1 );
    $res2=$GLOBALS['db']->query($ppj_bid_sql);
    if(!$res2){
        var_dump(出价记录添加失败); exit;
    }

    //选取订单添加
    $order_num=rand(2,5);
    for($i=0;$i<$order_num;$i++){
        $order_one_id=rand(0,count($ppj_row)-1);
        $order_arr[]=$ppj_row[$order_one_id];
    }

    $oi_sql = "INSERT INTO ".$GLOBALS['ecs']->table('order_info')." (order_sn,user_id,order_status,shipping_status,pay_status,consignee,country,province,city,district,mobile,pay_id,pay_name,goods_amount,money_paid,order_amount,referer,add_time,pay_time,extension_code,extension_id,ppj_id,ppj_no) VALUE ";
    $og_sql="INSERT INTO ".$GLOBALS['ecs']->table('order_goods')."(user_id,goods_id,goods_name,goods_sn,goods_number,market_price,goods_price,send_number,is_real,warehouse_id,area_id,ppj_no,order_sn) VALUES";
    $out_logs_sql = "INSERT INTO ".$GLOBALS['ecs']->table('goods_inventory_logs')."(goods_id,use_storage,admin_id,number,add_time,batch_number,order_sn) VALUES ";

    foreach($order_arr as $okey=>$oval){

        $sel_g_sql ="SELECT goods_id,goods_name,goods_sn,cost_price,shop_price,market_price FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id=".$oval['goods_id'];
        $goods_row=$GLOBALS['db']->getRow($sel_g_sql);

        $u_sql='SELECT u.user_id,ua.consignee,ua.country,ua.province,ua.city,ua.district,ua.mobile FROM '.$GLOBALS['ecs']->table('users').' AS u LEFT JOIN '.$GLOBALS['ecs']->table('user_address').'AS ua ON u.user_id=ua.user_id  WHERE u.user_id='.$oval['user_id'];
        $user_row=$GLOBALS['db']->getRow($u_sql);

        $order_sn=$year.$mouth.$days.str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

        $price=$oval['pay_price'];
        $pay_name="在线支付";
        $goods_id_arr[]=$oval['goods_id'];
        $ordersn_arr[]=$order_sn;
        $order_arr[$okey]['order_sn']=$order_sn;
        $extension_code="paipai_buy";
        $referer='touch';
        $goods_number='1';
        $send_number='1';

        $oi_sql .= "('".$order_sn."','".$oval['user_id']."',".'1'.",".'0'.",".'2'.",'".$user_row['consignee']."','". $user_row['country']."','".$user_row['province']."','".$user_row['city']."','".$user_row['district']."','".$user_row['mobile']."',".'15'.",'".$pay_name."','".$price."','".$price."','".$price."','".$referer."','".$now_time."','".$now_time."','".$extension_code."','".$oval['ppj_id']."','".$oval['ppj_id']."','".$oval['ppj_no']."'),";
        $og_sql.="('".$oval['user_id']."','".$oval['goods_id']."','".$goods_row['goods_name']."','".$goods_row['goods_sn']."','".$goods_number."','".$goods_row['market_price']."','".$goods_row['shop_price']."','".$send_number."',".'1'.",".'2'.",".'24'.",'".$oval['ppj_no']."','".$order_sn."'),";
        $out_logs_sql .= "( '".$oval['goods_id']."',".'8'.",".'59'.",".'-1'.",'".$now_time."','".$now_time."','".$order_sn."'),";

    }

    $oi_sql = substr( $oi_sql,0, strlen($oi_sql)-1 );
    $og_sql = substr( $og_sql,0, strlen($og_sql)-1 );

    $out_logs_sql = substr( $out_logs_sql,0, strlen($out_logs_sql)-1 );
    $oires=$GLOBALS['db']->query($oi_sql);
    if($oires){
        $ogres=$GLOBALS['db']->query($og_sql);
        if($ogres){
            $lres=$GLOBALS['db']->query($out_logs_sql);
            if(!$lres){
                var_dump(出库记录添加失败); exit;
            }
        }else{
            var_dump(订单产品添加失败); exit;
        }
    }else{
        var_dump(订单添加失败); exit;
    }

    $order_allid_sql = "SELECT order_id,order_sn FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_sn IN (".implode(",", $ordersn_arr).") ORDER BY order_id DESC ";
    $update_data=$GLOBALS['db']->getAll($order_allid_sql);
    $rec_id_sql = "SELECT rec_id,order_sn,goods_id FROM " . $GLOBALS['ecs']->table('order_goods') . " WHERE order_sn IN (".implode(",", $ordersn_arr).") ORDER BY rec_id DESC ";
    $rec_id_row = $GLOBALS['db']->getALL($rec_id_sql);
    $logs_id_sql = "SELECT id,order_sn FROM " . $GLOBALS['ecs']->table('goods_inventory_logs') . " WHERE order_sn IN (".implode(",", $ordersn_arr).") ORDER BY id DESC ";
    $logs_id_row = $GLOBALS['db']->getALL($logs_id_sql);
    foreach($update_data as $ukey2=>$uval2) {
        if($uval2['order_sn']==$rec_id_row[$ukey2]['order_sn']){
            $update_data[$ukey2]['rec_id']=$rec_id_row[$ukey2]['rec_id'];
            $rec_id_arr[] = $rec_id_row[$ukey2]['rec_id'];
        }
        if($uval2['order_sn']==$logs_id_row[$ukey2]['order_sn']){
            $update_data[$ukey2]['logs_id']=$logs_id_row[$ukey2]['id'];
            $logs_id_arr[] = $logs_id_row[$ukey2]['id'];
        }
    }

    //更改order_info下的order_id   更改goods_logs下的order_id
    $up_og_sql="UPDATE  ".$GLOBALS['ecs']->table('order_goods')." SET  order_id= CASE rec_id";
    $up_gl_sql="UPDATE  ".$GLOBALS['ecs']->table('goods_inventory_logs')." SET  order_id= CASE id";
    foreach($update_data as $okey2=>$oval2) {
        $up_og_sql.=" WHEN ".$oval2['rec_id']." THEN ". $oval2['order_id'];
        $up_gl_sql.=" WHEN ".$oval2['logs_id']." THEN ". $oval2['order_id'];
    }
    $up_og_sql.=" END WHERE rec_id IN(".implode(",", $rec_id_arr).") ";
    $up_gl_sql.=" END WHERE id IN(".implode(",", $logs_id_arr).") ";

    $uog=$GLOBALS['db']->query($up_og_sql);
    if($uog){
        $ugl=$GLOBALS['db']->query($up_gl_sql);
        if(!$ugl){
            var_dump(更改出库记录失败); exit;
        }
    }else{
        var_dump(更改订单记录失败); exit;
    }

    //更改goods_bid_user下的is_status   更改paipai_list下的goods_count
    for($x=0;$x<count($order_arr);$x++){
        $up_gb_sql="UPDATE  ".$GLOBALS['ecs']->table('paipai_goods_bid_user')." SET  is_status='1' WHERE spm_id=".$order_arr[$x]['spm_id'];
        $GLOBALS['db']->query($up_gb_sql);
        $gcount=$order_arr[$x]['goods_count']-1;
        $up_ppl_sql="UPDATE  ".$GLOBALS['ecs']->table('paipai_list')." SET  goods_count=".$gcount." WHERE ppj_id=".$order_arr[$x]['ppj_id'];
        $ppl=$GLOBALS['db']->query($up_ppl_sql);
    }
    if($ppl){
        return 1;
    }

}
function exchange_add_order($oi_row){

    $batch_number=time();
    $oi_sql = "INSERT INTO ".$GLOBALS['ecs']->table('order_info')." (order_sn,user_id,order_status,shipping_status,pay_status,consignee,country,province,city,district,mobile,pay_id,pay_name,goods_amount,integral,integral_money,referer,add_time,confirm_time,pay_time,extension_code,extension_id) VALUES ";
    $og_sql="INSERT INTO ".$GLOBALS['ecs']->table('order_goods')."(user_id,goods_id,goods_name,goods_sn,market_price,is_real,warehouse_id,area_id,order_sn) VALUES";
    $out_logs_sql = "INSERT INTO ".$GLOBALS['ecs']->table('goods_inventory_logs')."(goods_id,use_storage,admin_id,number,add_time,batch_number,order_sn) VALUES ";
    foreach($oi_row as $key=>$val){

        $pay_name="在线支付";
        $referer='touch';
        $goods_id_arr[]=$val['extension_id'];
        $order_sn_arr[]=$val['order_sn'];

        $oi_sql .= "('".$val['order_sn']."','".$val['user_id']."',".'1'.",".'2'.",".'2'.",'".$val['consignee']."','". $val['country']."','".$val['province']."','".$val['city']."','".$val['district']."','".$val['mobile']."',".'15'.",'".$pay_name."','".$val['goods_amount']."','".$val['integral']."','".$val['integral_money']."','".$referer."','".$val['add_time']."','".$val['confirm_time']."','".$val['pay_time']."','".$val['extension_code']."','".$val['extension_id']. "'),";
        $og_sql.="('".$val['user_id']."','".$val['extension_id']."','".$val['goods_name']."','".$val['ordere_sn']."','".$val['market_price']."',".'1'.",".'2'.",".'24'.",'".$val['order_sn']."'),";
        $out_logs_sql .= "( '".$val['extension_id']."',".'1'.",".'0'.",".'-1'.",'".$val['pay_time']."','".$batch_number."','".$val['order_sn']."'),";
    }
    $oi_sql = substr( $oi_sql,0, strlen($oi_sql)-1 );
    $og_sql = substr( $og_sql,0, strlen($og_sql)-1 );
    $out_logs_sql = substr( $out_logs_sql,0, strlen($out_logs_sql)-1 );
    $res1=$GLOBALS['db']->query($oi_sql);
    if($res1){
        $res2=$GLOBALS['db']->query($og_sql);
        if($res2){
            $res3=$GLOBALS['db']->query($out_logs_sql);
        }
    }

    if(count($oi_row)%1000==0){
        $GLOBALS['db']->query('commit');
        $GLOBALS['db']->query('begin');
    }
    $GLOBALS['db']->query('commit');

    if($res3){
        return '1';
    }
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
    $cat_id=$_POST['cat_id'];
    $supplier_id=$_POST['supplier_id'];

    $year=2019;
    $order_time=$year.'-'.$mouth.'-'.$days;
    $S=rand(8,18);//随机--时
    $F=rand(0,59);//随机--分
    $M=rand(0,59);//随机--秒
    $add_time=strtotime($order_time." ".$S.":".$F.":".$M);

    

    if($stoprice && $mouth && $days) {
        var_dump(date("H:i:s",time()+8*3600));
        if($cat_id){
            $cat_one_sql="SELECT cat_id,cat_name FROM ".$GLOBALS['ecs']->table('category')." WHERE parent_id=" . $cat_id." AND is_show=1 ";
            $cat_one_row=$GLOBALS['db']->getRow($cat_one_sql);
            if(!$cat_one_row){
                var_dump("输入的类别错误"); exit;
            }


            $cat2_sql = "SELECT cat_id,cat_name,parent_id FROM " . $GLOBALS['ecs']->table('category') . " WHERE parent_id=" . $cat_id." AND is_show=1 ";
            $cat2_row = $GLOBALS['db']->getAll($cat2_sql);

            foreach ($cat2_row as $cakey => $catval) {
                $goods_sql = "SELECT count(goods_id) as count FROM " . $GLOBALS['ecs']->table('goods') . " WHERE cat_id=" . $catval['cat_id'];
                $goods_count[] = $GLOBALS['db']->getAll($goods_sql);
                $goods_sql2 = "SELECT goods_id,cat_id,cost_price FROM " . $GLOBALS['ecs']->table('goods') . " WHERE cat_id=" . $catval['cat_id']." ORDER BY shop_price DESC";
                $goods_row[] = $GLOBALS['db']->getAll($goods_sql2);
            }
        }else if($supplier_id){
            $sc_goods_sql = "SELECT count(goods_id) as count FROM " . $GLOBALS['ecs']->table('goods') . "  WHERE suppliers_id=" . $supplier_id;
            $goods_count[] = $GLOBALS['db']->getAll($sc_goods_sql);
            $s_goods_sql = "SELECT goods_id,cat_id,cost_price FROM " . $GLOBALS['ecs']->table('goods') . " WHERE suppliers_id=" . $supplier_id." ORDER BY shop_price DESC";
            $goods_row[] = $GLOBALS['db']->getAll($s_goods_sql);
        }

        if($goods_count[0][0]['count'] == '0'){
              var_dump("商品数量为空"); exit;
        }

        for ($i = 0; $i < count($goods_row); $i++) {
            $new_goods = $goods_row[$i];
            for ($y = 0; $y < count($new_goods); $y++) {
                $new2_goods[] = $new_goods[$y];
            }
        }

        for ($i = 0; $i < count($new2_goods); $i++) {
            $goods_cost_total += $new2_goods[$i]['cost_price'];
        }

        $first_price_yu=$stoprice%$goods_cost_total;

        $first_goods_num=($stoprice-$first_price_yu)/$goods_cost_total;

        foreach ($new2_goods as $ngkey => $ngval) {
              $goods_row2[]=array(
                  'goods_id' => $ngval['goods_id'],
                  'cost_price' => $ngval['cost_price'],
//                  'total_price' => $goods_count_price[$g + 1],
                  'goods_number'=>round($first_goods_num),
                  'one_price'=>round($ngval['cost_price']*$first_goods_num)
              );
        }
//        var_dump($goods_row2);
        for ($t = 0; $t < count($goods_row2); $t++) {
            $first_price_yu-=$goods_row2[$t]['cost_price'];
            if($first_price_yu > $goods_row2[$t]['cost_price']){
                 $goods_id[]=$goods_row2[$t]['goods_id'];
            }

        }
        foreach($goods_row2 as $grkey=>$grval){
            if($grval['goods_id'] == $goods_id[$grkey]){
                $goods_row2[$grkey]['goods_number']=$grval['goods_number']+1;
            }
        }

        $goods_logs_sql = "INSERT INTO " . $GLOBALS['ecs']->table('goods_inventory_logs') . "(goods_id,use_storage,admin_id,number,product_id,add_time) VALUES ";
        foreach ($goods_row2 as $key4 => $val4) {
            $sel_sql = "SELECT goods_number FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id=" . $val4['goods_id'];
            $old_goods_num = $GLOBALS['db']->getRow($sel_sql);
            if($old_goods_num['goods_number']){
                $goods_number=$val4['goods_number']+$old_goods_num['goods_number'];
            }else{
                $goods_number = $val4['goods_number'];
            }

          $upd_sql="UPDATE ".$GLOBALS['ecs']->table('goods')." SET goods_number=".$goods_number." WHERE goods_id=".$val4['goods_id'];
          $GLOBALS['db']->query($upd_sql);

          $goods_logs_sql .= "( '" . $val4['goods_id'] . "'," . '7' . "," . '59' . ",'" . $val4['goods_number'] . "'," . '0' . ",'" . $add_time ."'),";
        }
        $goods_logs_sql = substr($goods_logs_sql, 0, strlen($goods_logs_sql) - 1);
        $GLOBALS['db']->query($goods_logs_sql);
        var_dump(date("H:i:s",time()+8*3600));
        var_dump("添加成功");
    }else{
        var_dump("q请填写有效数据");
    }

    $smarty->display('paipai_part_order.dwt');
}
elseif($_REQUEST['act'] == 'outorder') {

    $cat_sql = "SELECT cat_id,cat_name FROM " . $GLOBALS['ecs']->table('category') . " WHERE parent_id=0 AND is_show=1";
    $cat_row = $GLOBALS['db']->getAll($cat_sql);
    $smarty->assign('cat_row', $cat_row);

    $year = $_POST['year'];
    $mouth = $_POST['mouth'];
//    $days = $_POST['days'];
    $outprice = $_POST['out_price'];
    $saleprice = $_POST['sale_price'];
    $cat_id = $_POST['cat_id'];
    $supplier_id=$_POST['supplier_id'];

    var_dump(date("H:i:s", time() + 8 * 3600));
    if ($outprice && $mouth) {
        if($cat_id) {
            $cat_one_sql = "SELECT cat_id,cat_name FROM " . $GLOBALS['ecs']->table('category') . " WHERE parent_id=" . $cat_id . " AND is_show=1 ";
            $cat_one_row = $GLOBALS['db']->getRow($cat_one_sql);
            if (!$cat_one_row) {
                var_dump("输入的类别错误");
                exit;
            }

            $cat2_sql = "SELECT cat_id,cat_name,parent_id FROM " . $GLOBALS['ecs']->table('category') . " WHERE parent_id=" . $cat_id . " AND is_show=1 ";
            $cat2_row = $GLOBALS['db']->getAll($cat2_sql);
            foreach ($cat2_row as $key => $catval) {
                $goods_sql = "SELECT count(goods_id) as count FROM " . $GLOBALS['ecs']->table('goods') . " WHERE cat_id=" . $catval['cat_id'];
                $goods_count[] = $GLOBALS['db']->getAll($goods_sql);
                $goods_sql2 = "SELECT goods_id,cat_id,goods_number,cost_price,shop_price FROM " . $GLOBALS['ecs']->table('goods') . " WHERE cat_id=" . $catval['cat_id'];
                $goods_row[] = $GLOBALS['db']->getAll($goods_sql2);
            }
        }else if($supplier_id){
            $sc_goods_sql = "SELECT count(goods_id) as count FROM " . $GLOBALS['ecs']->table('goods') . "  WHERE suppliers_id=" . $supplier_id;
            $goods_count[] = $GLOBALS['db']->getAll($sc_goods_sql);
            $s_goods_sql = "SELECT goods_id,cat_id,cost_price FROM " . $GLOBALS['ecs']->table('goods') . " WHERE suppliers_id=" . $supplier_id." ORDER BY shop_price DESC";
            $goods_row[] = $GLOBALS['db']->getAll($s_goods_sql);
        }

        if ($goods_count[0][0]['count'] == '0') {
            var_dump("商品数量为空");
            exit;
        }


        for ($i = 0; $i < count($goods_row); $i++) {
            $new_goods = $goods_row[$i];
            for ($y = 0; $y < count($new_goods); $y++) {
                $new2_goods[] = $new_goods[$y];
            }
        }
  

        for ($i = 0; $i < count($new2_goods); $i++) {
            $goods_cost_total += $new2_goods[$i]['cost_price'];
        }

        $first_price_yu=$outprice%$goods_cost_total;

        $first_goods_num=($outprice-$first_price_yu)/$goods_cost_total;


        for($ng=0;$ng<count($new2_goods);$ng++){
            if($new2_goods[$ng]['goods_number']<round($first_goods_num)){
                unset($new2_goods[$ng]);
            } 
        }

        foreach ($new2_goods as $ngkey => $ngval) {
              $goods_row2[]=array(
                  'goods_id' => $ngval['goods_id'],
                  'cost_price' => $ngval['cost_price'],
                  'goods_number'=>round($first_goods_num),
              );
        }

        for ($t = 0; $t < count($goods_row2); $t++) {
            $first_price_yu-=$goods_row2[$t]['cost_price'];
            if($first_price_yu > $goods_row2[$t]['cost_price']){
                 $goods_id[]=$goods_row2[$t]['goods_id'];
            }
        }

        foreach($goods_row2 as $grkey=>$grval){
            if($grval['goods_id'] == $goods_id[$grkey]){
                $goods_row2[$grkey]['goods_number']=$grval['goods_number']+1;
            }
        }

        $sale_pro=$saleprice/$outprice;

        $days =cal_days_in_month(CAL_GREGORIAN, $mouth, $year);
        foreach ($goods_row2 as $gdkey => $gdval){
            for($gi=1;$gi<=$gdval['goods_number'];$gi++){

                $d=rand(1,$days);
                if($days<10){
                    $d='0'.$d;
                }
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
                //买家
                $user_id=rand(40079,861288);
                $u_sql='SELECT u.user_id,ua.consignee,ua.country,ua.province,ua.city,ua.district,ua.mobile FROM '.$GLOBALS['ecs']->table('users').' AS u LEFT JOIN '.$GLOBALS['ecs']->table('user_address').'AS ua ON u.user_id=ua.user_id  WHERE u.user_id='.$user_id;
                $user_row=$GLOBALS['db']->getRow($u_sql);
                if(!$user_row['consignee']){
                    $user_id=32527;
                    $u_sql='SELECT u.user_id,ua.consignee,ua.country,ua.province,ua.city,ua.district,ua.mobile FROM '.$GLOBALS['ecs']->table('users').' AS u LEFT JOIN '.$GLOBALS['ecs']->table('user_address').'AS ua ON u.user_id=ua.user_id  WHERE u.user_id='.$user_id;
                    $user_row=$GLOBALS['db']->getRow($u_sql);
                }
                //商品
                $g_sql='SELECT goods_id,goods_name,goods_sn,market_price,shop_price,goods_number FROM '.$GLOBALS['ecs']->table('goods').' WHERE goods_id='.$gdval['goods_id'];
                $goods_row=$GLOBALS['db']->getRow($g_sql);
                //单个商品的单价
                $one_price =@ str_replace(',', '', number_format($goods_sale_num[$s + 1] / $gdval['out_num'], 2));
                $sale_data[] = array(
                    'goods_id' => $gdval['goods_id'],
                    'shop_price' => $gdval['shop_price'],
                    'user_id'=>$user_row['user_id'],
                    'consignee'=>$user_row['consignee'],
                    'country'=>$user_row['country'],
                    'province'=>$user_row['province'],
                    'city'=>$user_row['city'],
                    'district'=>$user_row['district'],
                    'mobile'=>$user_row['mobile'],
                    'goods_name'=>$goods_row['goods_name'],
                    'market_price'=>$goods_row['market_price'],
                    'shop_price'=>$goods_row['shop_price'],
                    'goods_number'=>$goods_row['goods_number']-$gdval['goods_number'],
                    'add_time'=>$add_time,
                    'pay_time'=>$pay_time,
                    'confirm_time'=>$confirm_time,
                    'shipping_time'=>$shipping_time,
                    'take_time'=>$take_time,
                    'first_number'=>$gdval['goods_number'],
//                    'cost_total_price' => $gdval['cost_total_price'],
                    // 'out_num' => $gdval['out_num'],
//                    'sale_total_price' => $goods_sale_num[$s + 1],
                    'sale_one_price' => str_replace(',', '', number_format(rand($gdval['cost_price']*$sale_pro * 0.95, $gdval['cost_price']*$sale_pro * 1.05),2)),
                );
            }
        }
        $ceil_order_num=$first_goods_num;
        $row = order_add($sale_data,$year, $mouth,$ceil_order_num);
        if($row){
            $links = array(
                array('href' => 'paipai_part.php?act=list', 'text' => $_LANG['back_list']) //返回列表
            );
            sys_msg($_LANG['add_success'], 0, $links);  //编辑成功
        }
    } else {
        var_dump("q请填写有效数据");
    }

    $smarty->display('paipai_part_outorder.dwt');
}
elseif($_REQUEST['act'] =='upordernext'){

    $year=$_POST['year'];
    $mouth = $_POST['mouth'];
//    $days=$_POST['days'];
    $days =cal_days_in_month(CAL_GREGORIAN, $mouth, $year);

    $min_time=$year.'-'.$mouth.'-01'.' 00:00:00';
    $max_time=$year.'-'.$mouth.'-'.$days.' 23:59:59';
    $limit_min_time=strtotime($min_time);
    $limit_max_time=strtotime($max_time);
    var_dump(date("H:i:s", time() + 8 * 3600));
    if($mouth){
        $order_sql="SELECT oi.order_id,oi.order_sn FROM ".$GLOBALS['ecs']->table('order_info')." AS oi LEFT JOIN ".$GLOBALS['ecs']->table('order_goods')." AS og ON oi.order_sn=og.order_sn WHERE oi.add_time<=".$limit_max_time." AND oi.add_time>=".$limit_min_time.' AND og.order_id=0 ORDER BY oi.order_id DESC';
        $update_data=$GLOBALS['db']->getAll($order_sql);

        foreach($update_data as $ukey=>$uval) {
            $order_sn_arr[] = $uval['order_sn'];
        }
        $rec_id_sql = "SELECT rec_id,order_sn,goods_id FROM " . $GLOBALS['ecs']->table('order_goods') . " WHERE order_sn IN (".implode(",", $order_sn_arr).")".'  ORDER BY rec_id DESC';
        $rec_id_row = $GLOBALS['db']->getALL($rec_id_sql);
        $logs_id_sql = "SELECT id,order_sn FROM " . $GLOBALS['ecs']->table('goods_inventory_logs') . " WHERE order_sn IN (".implode(",", $order_sn_arr).")".'  ORDER BY id DESC';
        $logs_id_row = $GLOBALS['db']->getALL($logs_id_sql);
        foreach($update_data as $key=>$val) {
            if($val['order_sn']==$rec_id_row[$key]['order_sn']){
                $update_data[$key]['rec_id']=$rec_id_row[$key]['rec_id'];
                $update_data[$key]['goods_id']=$rec_id_row[$key]['goods_id'];
                $rec_id_arr[] = $rec_id_row[$key]['rec_id'];
                $order_id_arr[] = $val['order_id'];
                $goods_id_arr[] = $rec_id_row[$key]['goods_id'];
            }
            if($val['order_sn']==$logs_id_row[$key]['order_sn']){
                $update_data[$key]['logs_id']=$logs_id_row[$key]['id'];
                $logs_id_arr[] = $logs_id_row[$key]['id'];
            }
        }

       //更改order_info下的order_id
        $up_og_sql="UPDATE  ".$GLOBALS['ecs']->table('order_goods')." SET  order_id= CASE rec_id";
        foreach($update_data as $key2=>$val2) {
           $up_og_sql.=" WHEN ".$val2['rec_id']." THEN ". $val2['order_id'];
        }
        $up_og_sql.=" END WHERE rec_id IN(".implode(",", $rec_id_arr).") ";
        $GLOBALS['db']->query($up_og_sql);
        //更改goods_logs下的order_id
        $up_gl_sql="UPDATE  ".$GLOBALS['ecs']->table('goods_inventory_logs')." SET  order_id= CASE id";
        foreach($update_data as $key3=>$val3) {
            $up_gl_sql.=" WHEN ".$val3['logs_id']." THEN ". $val3['order_id'];
        }
        $up_gl_sql.=" END WHERE id IN(".implode(",", $logs_id_arr).") ";
        $GLOBALS['db']->query($up_gl_sql);
        if(count($update_data)%1000==0){
            $GLOBALS['db']->query('commit transaction');
            $GLOBALS['db']->query('begin');
        }
        $GLOBALS['db']->query('commit');
        //更改order_info下的ppj_id
        $ppj_goods_id_row=array_values(array_unique($goods_id_arr));
        $paipai_sql = "SELECT ppj_id,ppj_no,goods_id FROM " . $GLOBALS['ecs']->table('paipai_list') . " WHERE  start_time>=" . $limit_min_time . " AND end_time<=" . $limit_max_time." AND goods_id IN (".implode(",", $ppj_goods_id_row).")";
        $paipai_row = $GLOBALS['db']->getAll($paipai_sql);

        foreach($update_data as $key4=>$val4) {
            foreach($paipai_row as $pkey=>$pval){
                if($val4['goods_id'] == $pval['goods_id']){
                    $update_data[$key4]['ppj_id']= $pval['ppj_id'];
                    $update_data[$key4]['ppj_no']= $pval['ppj_no'];
                }
            }
        }
        $up_oi_sql="UPDATE  ".$GLOBALS['ecs']->table('order_info')." SET  ppj_id= CASE order_id";
        foreach($update_data as $key5=>$val5) {
            $up_oi_sql.=" WHEN ".$val5['order_id']." THEN ". $val5['ppj_id'];
        }
        $up_oi_sql.=" END WHERE order_id IN(".implode(",", $order_id_arr).") ";
        $GLOBALS['db']->query($up_oi_sql);
        if(count($update_data)%1000==0){
            $GLOBALS['db']->query('commit transaction');
            $GLOBALS['db']->query('begin');
        }
        $res6=$GLOBALS['db']->query('commit');
        if($res6){
            $links = array(
                array('href' => 'paipai_part.php?act=list', 'text' => $_LANG['back_list']) //返回列表
            );
            sys_msg($_LANG['add_success'], 0, $links);  //编辑成功
        }


    }

    $smarty->display('paipai_part_outordernext.dwt');

}
elseif($_REQUEST['act'] =='paipaiact'){

    $mouth = $_POST['mouth'];
    $days = $_POST['days'];
    $out_price = $_POST['out_price'];
    $goods_number=$_POST['goods_number'];
    $year='2019';

    $min_time=$year.'-'.$mouth.'-'.$days.' 07:00:00';
    $max_time=$year.'-'.$mouth.'-'.$days.' 22:00:00';
    $limit_min_time=strtotime($min_time);
    $limit_max_time=strtotime($max_time);
    var_dump(date("H:i:s", time() + 8 * 3600));
    if($mouth && $days && $out_price && $goods_number){

        $num=explode(";",$goods_number);

        for($i=0;$i<count($num);$i++){
            $num2[]=explode(",",$num[$i]);
        }

        for($t=0;$t<count($num2);$t++){
            $goods_sql=" SELECT goods_id FROM ".$GLOBALS['ecs']->table('goods')." WHERE suppliers_id=".$num2[$t][0];
            $goods_row=$GLOBALS['db']->getAll($goods_sql);

            if(count($goods_row)<$num2[$t][1]){
                for($g=0;$g<count($goods_row);$g++){
                    $goods_id_row1[]=$goods_row[$g]['goods_id'];
                }
            }else{
                for($g=0;$g<count($goods_row);$g++){
                    $goods_id_row2[]=$goods_row[$g]['goods_id'];
                    $number=$num2[$t][1];
                }
                $goods_id_row4=array_rand($goods_id_row2,$number);
                foreach ($goods_id_row4 as $val2) {
                    $goods_id_row5[]=$goods_id_row2[$val2];
                }
            }
        }
        if($goods_id_row5){
            $goods_id_row=array_merge($goods_id_row1,$goods_id_row5);
        }else{
            $goods_id_row=$goods_id_row1;
        }


        //活动添加
        $goods_sql2=" SELECT goods_id,goods_name,cost_price,goods_sn,market_price,shop_price,goods_number FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id in(".implode(",", $goods_id_row).")";
        $goods_data=$GLOBALS['db']->getAll($goods_sql2);
        $ppj_add_sql="INSERT INTO ".$GLOBALS['ecs']->table('paipai_list')."(ppj_name,goods_id,ppj_no,goods_count,ppj_start_fee,ppj_buy_fee,start_time,end_time,ppj_margin_fee,ppj_startpay_time,ppj_endpay_time,ppj_createtime,goods_name,is_hot,is_new,review_status,ext_info,user_id,ppj_staus) VALUES";
        foreach($goods_data as $key=>$val){
            //最近一期商品的拍拍活动
            $act_sql="SELECT ppj_no FROM ".$GLOBALS['ecs']->table('paipai_list')." WHERE goods_id=".$val['goods_id']." ORDER BY ppj_id DESC";
            $ppj_goods=$GLOBALS['db']->getRow($act_sql);

            $ppj_no=$ppj_goods['ppj_no']+1;

            $goods_count='300';
            $ppj_margin_fee="0.00";

            if($val['shop_price'] <=100){
                $part='5';
            }elseif($val['shop_price'] >100 && $val['shop_price'] <300){
                $part='8';
            }elseif($val['shop_price'] >=300 && $val['shop_price'] <500){
                $part='11';
            }else{
                $part='15';
            }
            $ppj_buy_fee=$val['cost_price']+($val['cost_price']*0.25);
            $price_part=floor($val['shop_price']*0.7/$part);
            $ceil_amount=floor($goods_count/$part);
            for($ei=1;$ei<$part;$ei++){
                $price_ladder[$ei]=array(
                    'amount'=>$ei==1?'1':$ceil_amount*($ei-1),
                    'price'=>$ppj_buy_fee+($price_part*($ei-1))
                );
            }
            $price_ladder_arr[]=array(
                'goods_id'=>$val['goods_id'],
                'part'=>$part,
                'goods_name'=>$val['goods_name'],
                'price_ladder'=>$price_ladder,
                'cost_price'=>$val['cost_price'],
                'shop_price'=>$val['shop_price'],
                'goods_sn'=>$val['goods_sn'],
                'market_price'=>$val['market_price'],
                'goods_number'=>$val['goods_number'],
            );
            $ext_info=serialize(array('price_ladder' => $price_ladder, 'restrict_amount' => $goods_count));
            $ppj_start_fee='0.00';
            $shop_price_total+=$val['shop_price'];

            $ppj_add_sql.="('".$val['goods_name']."','".$val['goods_id']."','".$ppj_no."','".$goods_count."','".$ppj_start_fee."','".$ppj_buy_fee."','".$limit_min_time."','".$limit_max_time."','".$ppj_margin_fee."',".'20'.",".'20'.",'".$limit_min_time."','".$val['goods_name']."',".'0'.",".'0'.",".'3'.",'".$ext_info."',".'0'.",".'2'."),";
        }
//        var_dump('销售成本'.$shop_price_total);
        $first_price_yu=$out_price%$shop_price_total;
//        var_dump('余'.$first_price_yu);
        $first_goods_num=($out_price-$first_price_yu)/$shop_price_total;
//        var_dump('单'.round($first_goods_num));

        foreach ($price_ladder_arr as $key => $val) {
            $price_ladder_arr[$key]['goods_number']=round($first_goods_num);
        }
        for ($t = 0; $t < count($price_ladder_arr); $t++) {
            $first_price_yu-=$price_ladder_arr[$t]['shop_price'];
            if($first_price_yu > $price_ladder_arr[$t]['shop_price']){
                $goods_id[]=$price_ladder_arr[$t]['goods_id'];
            }
        }
        foreach($price_ladder_arr as $grkey=>$grval){
            if($grval['goods_id'] == $goods_id[$grkey]){
                $price_ladder_arr[$grkey]['cut_goods_number']=$grval['goods_number']+1;
            }
        }
        $ppj_add_sql = substr( $ppj_add_sql,0, strlen($ppj_add_sql)-1 );
        $res=$GLOBALS['db']->query($ppj_add_sql);
        $res=1;
        if($res) {
            foreach ($price_ladder_arr as $gdkey => $gdval) {

                for ($gi = 1; $gi <= $gdval['cut_goods_number']; $gi++) {
                    $num=round($gdval['cut_goods_number']/count($gdval['price_ladder']));
                    if($gi >=1 && $gi<= $num){
                        $price=$gdval['price_ladder'][1]['price'];
                    }elseif($gi >$num && $gi<= $num*2){
                        $price=$gdval['price_ladder'][2]['price'];
                    }elseif($gi >$num*2 && $gi<= $num*3){
                        $price=$gdval['price_ladder'][3]['price'];
                    }elseif($gi >$num*3 && $gi<= $num*4){
                        $price=$gdval['price_ladder'][4]['price'];
                    }elseif($gi >$num*4 && $gi<= $num*5){
                        $price=$gdval['price_ladder'][5]['price'];
                    }elseif($gi >$num*6 && $gi<= $num*7){
                        $price=$gdval['price_ladder'][7]['price'];
                    }elseif($gi >$num*7&& $gi<= $num*8){
                        $price=$gdval['price_ladder'][8]['price'];
                    }elseif($gi >$num*8 && $gi<= $num*9){
                        $price=$gdval['price_ladder'][9]['price'];
                    }elseif($gi >$num*9 && $gi<= $num*10){
                        $price=$gdval['price_ladder'][10]['price'];
                    }elseif($gi >$num*10 && $gi<= $num*11){
                        $price=$gdval['price_ladder'][11]['price'];
                    }elseif($gi >$num*11 && $gi<= $num*12){
                        $price=$gdval['price_ladder'][12]['price'];
                    }elseif($gi >$num*12 && $gi<= $num*13){
                        $price=$gdval['price_ladder'][13]['price'];
                    }elseif($gi >$num*13){
                        $price=$gdval['price_ladder'][14]['price'];
                    }

                    $order_date=ordertime($year,$mouth,$days);
                    //买家
                    $user_id = rand(40079, 861288);
                    $u_sql = 'SELECT u.user_id,ua.consignee,ua.country,ua.province,ua.city,ua.district,ua.mobile FROM ' . $GLOBALS['ecs']->table('users') . ' AS u LEFT JOIN ' . $GLOBALS['ecs']->table('user_address') . 'AS ua ON u.user_id=ua.user_id  WHERE u.user_id=' . $user_id;
                    $user_row = $GLOBALS['db']->getRow($u_sql);
                    if (!$user_row['consignee']) {
                        $user_id = 32527;
                        $u_sql = 'SELECT u.user_id,ua.consignee,ua.country,ua.province,ua.city,ua.district,ua.mobile FROM ' . $GLOBALS['ecs']->table('users') . ' AS u LEFT JOIN ' . $GLOBALS['ecs']->table('user_address') . 'AS ua ON u.user_id=ua.user_id  WHERE u.user_id=' . $user_id;
                        $user_row = $GLOBALS['db']->getRow($u_sql);
                    }
                    $order_sn=$year.$mouth.$days.str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
                    $sale_data[] = array(
                        'order_sn'=>$order_sn,
                        'goods_id' => $gdval['goods_id'],
                        'goods_sn' => $gdval['goods_sn'],
                        'user_id' => $user_row['user_id'],
                        'consignee' => $user_row['consignee'],
                        'country' => $user_row['country'],
                        'province' => $user_row['province'],
                        'city' => $user_row['city'],
                        'district' => $user_row['district'],
                        'mobile' => $user_row['mobile'],
                        'goods_name' => $gdval['goods_name'],
                        'market_price' => $gdval['market_price'],
                        'shop_price' => $gdval['shop_price'],
                        'add_time' => $order_date['add_time'],
                        'pay_time' => $order_date['pay_time'],
                        'confirm_time' => $order_date['confirm_time'],
                        'shipping_time' => $order_date['shipping_time'],
                        'take_time' => $order_date['take_time'],
                        'goods_number'=>$gdval['goods_number']-$gdval['cut_goods_number'],
                        'sale_one_price' =>$price,
                        'cut_goods_num'=>$gdval['cut_goods_number']
                    );
                }
            }
            $row=paipai_order($sale_data);
            if($row){
                var_dump('成功');
                var_dump(date("H:i:s", time() + 8 * 3600));
            }else{
                var_dump('失败');
            }
        }
    }else{
        var_dump("输入有效数据");
    }
    $smarty->display('paipai_part_paipaiact.dwt');
}
elseif($_REQUEST['act'] =='uptime'){

    $mouth = $_POST['mouth'];
    $total_days =cal_days_in_month(CAL_GREGORIAN, $mouth, $year);
    if($mouth){
        $logs_sql = "SELECT id,add_time FROM " . $GLOBALS['ecs']->table('goods_inventory_logs') . " WHERE number>'-1' ";
        $logs_row = $GLOBALS['db']->getAll($logs_sql);

        $up_logs_sql="UPDATE ".$GLOBALS['ecs']->table('goods_inventory_logs')." SET add_time= CASE id";
        foreach($logs_row as $key=>$val){
            $up_logs_sql.=" WHEN ".$val['id']." THEN ". strtotime($val['add_time']);
            $logs_id[]=$val['id'];
        }
        $up_logs_sql.=" END WHERE id IN(".implode(",", $logs_id).") ";
        $GLOBALS['db']->query($up_logs_sql);
        if(count($logs_row)%1000==0){
            $GLOBALS['db']->query('commit transaction');
            $GLOBALS['db']->query('begin');
        }
        $GLOBALS['db']->query('commit');

    }else{
        var_dump("输入有效数据");
    }
    $smarty->display('paipai_part_uptime.dwt');
}
elseif($_REQUEST['act'] =='paipaiauto'){

    $year='2019';
    $mouth = $_POST['mouth'];
    $days = $_POST['days'];

    $min_time=$year.'-'.$mouth.'-'.$days.' 00:00:00';
    $max_time=$year.'-'.$mouth.'-'.$days.' 23:59:59';
    $limit_min_time=strtotime($min_time);
    $limit_max_time=strtotime($max_time);
    var_dump(date("H:i:s", time() + 8 * 3600));
    if($mouth && $days){
        $ppj_sql="SELECT ppj_id,ppj_no,goods_id,goods_count,ppj_buy_fee,ppj_now_fee,ext_info FROM ".$GLOBALS['ecs']->table('paipai_list')." WHERE end_time<=".$limit_max_time." AND start_time>=".$limit_min_time;
        $ppj_data=$GLOBALS['db']->getAll($ppj_sql);
        if(!$ppj_data){
            var_dump('暂无拍拍活动'); exit;
        }
        foreach($ppj_data as $key=>$val){
            $ppj_id_arr[]=$val['ppj_id'];
            $ppj_no_arr[]=$val['ppj_no'];
            $ppj_buy_fee_arr[]=$val['ppj_buy_fee'];
            $ppj_goods_id_arr[]=$val['goods_id'];
            $ppj_ext_info_arr[]=$val['ext_info'];
            $ppj_goods_count[]=$val['goods_count'];
        }
        $one_num=rand(5,10);
        for($i=0;$i<$one_num;$i++){
            $ppj_one_id=rand(0,count($ppj_id_arr)-1);
            $user_id=rand(40079,861288);
            $user_arr[]=$user_id;
            $ppj_row[]=array(
                'ppj_id'=>$ppj_id_arr[$ppj_one_id],
                'ppj_no'=>$ppj_no_arr[$ppj_one_id],
                'buy_fee'=>$ppj_buy_fee_arr[$ppj_one_id],
                'user_id'=>$user_id,
                'goods_id'=>$ppj_goods_id_arr[$ppj_one_id],
                'goods_count'=>$ppj_goods_count[$ppj_one_id],
                'ext_info'=>unserialize($ppj_ext_info_arr[$ppj_one_id])
            );
        }
        $row=ppj_auto($ppj_row,$year,$mouth,$days);
        if($row){
            var_dump("执行成功");
            var_dump(date("H:i:s", time() + 8 * 3600));
        }

    }else{
        var_dump("填写有效数据");
    }

    $smarty->display('paipai_part_paiauto.dwt');
}
elseif($_REQUEST['act'] =='paipai'){

    $year='2019';
    $mouth = '4';
    $days = '12';
//    $mouth = $_POST['mouth'];
//    $days = $_POST['days'];

    $min_time=$year.'-'.$mouth.'-'.$days.' 00:00:00';
    $max_time=$year.'-'.$mouth.'-'.$days.' 23:59:59';
    $limit_min_time=strtotime($min_time);
    $limit_max_time=strtotime($max_time);

    if($mouth && $days){

        $ppj_sql="SELECT ppj_id,ppj_no FROM ".$GLOBALS['ecs']->table('paipai_list')." WHERE end_time=<".$limit_max_time." AND start_time>=".$limit_min_time;
        $ppj_data=$GLOBALS['db']->getAll($ppj_sql);

        $ppj_sql="SELECT ppj_id,ppj_no,goods_id,goods_count,ppj_buy_fee,ppj_now_fee,ext_info FROM ".$GLOBALS['ecs']->table('paipai_list')." WHERE end_time<=".$limit_max_time." AND start_time>=".$limit_min_time;
        $ppj_data=$GLOBALS['db']->getAll($ppj_sql);
        if(!$ppj_data){
            exit(json_encode(array('row'=>'拍拍活动为空')));
        }
        foreach($ppj_data as $key=>$val){
            $ppj_id_arr[]=$val['ppj_id'];
            $ppj_no_arr[]=$val['ppj_no'];
            $ppj_buy_fee_arr[]=$val['ppj_buy_fee'];
            $ppj_goods_id_arr[]=$val['goods_id'];
            $ppj_ext_info_arr[]=$val['ext_info'];
            $ppj_goods_count[]=$val['goods_count'];
        }
        $one_num=rand(3,5);
        for($i=0;$i<$one_num;$i++){
            $ppj_one_id=rand(0,count($ppj_id_arr)-1);
            $user_id=rand(40079,861288);
            $user_arr[]=$user_id;
            $ppj_row[]=array(
                'ppj_id'=>$ppj_id_arr[$ppj_one_id],
                'ppj_no'=>$ppj_no_arr[$ppj_one_id],
                'buy_fee'=>$ppj_buy_fee_arr[$ppj_one_id],
                'user_id'=>$user_id,
                'goods_id'=>$ppj_goods_id_arr[$ppj_one_id],
                'goods_count'=>$ppj_goods_count[$ppj_one_id],
                'ext_info'=>unserialize($ppj_ext_info_arr[$ppj_one_id])
            );
        }
        $row=ppj_auto($ppj_row,$year,$mouth,$days);
  //      exit(json_encode(array('row'=>$one_num)));
    }else{
        exit(json_encode(array('row'=>'nonono')));
    }
}
elseif($_REQUEST['act'] =='exchange'){

    $year = $_POST['year'];
    $mouth = $_POST['mouth'];
    $outprice = $_POST['out_price'];

    if($year && $mouth && $outprice){
        $eg_sql='SELECT eg.goods_id,eg.exchange_integral,eg.market_integral,g.goods_name,g.goods_sn,g.market_price,g.cost_price FROM '. $GLOBALS['ecs']->table('exchange_goods').' AS eg LEFT JOIN '.$GLOBALS['ecs']->table('goods').' AS g ON eg.goods_id=g.goods_id WHERE eg.is_exchange=1 ';
        $eg_row=$GLOBALS['db']->getAll($eg_sql);

        $oi_uid_sql='SELECT distinct user_id,consignee,country,province,city,district,mobile FROM '. $GLOBALS['ecs']->table('order_info');
        $oi_uid_row=$GLOBALS['db']->getAll($oi_uid_sql);
        foreach($oi_uid_row as $uidkey=>$uidval){
            $uid_arr[]=array(
                'user_id'=>$uidval['user_id'],
                'consignee'=>$uidval['consignee'],
                'country'=>$uidval['country'],
                'province'=>$uidval['province'],
                'city'=>$uidval['city'],
                'district'=>$uidval['district'],
                'mobile'=>$uidval['mobile'],
            );
        }

        $eg_count=count($eg_row);

        $one_total_price=grouping($outprice,$eg_count,100);

        for($i=0;$i<count($one_total_price);$i++){
             $oi_data[]=array(
                 'goods_id'=>$eg_row[$i]['goods_id'],
                 'goods_name'=>$eg_row[$i]['goods_name'],
                 'goods_sn'=>$eg_row[$i]['goods_sn'],
                 'market_price'=>$eg_row[$i]['market_price'],
                 'exchange_integral'=>$eg_row[$i]['exchange_integral'],
                 'num'=> round($one_total_price[$i+1]/$eg_row[$i]['cost_price']),
             );
        }

        $days =cal_days_in_month(CAL_GREGORIAN, $mouth, $year);
        foreach($oi_data as $oikey=>$oival){
            for($n=0;$n<$oival['num'];$n++){
                 $uid_row=array_rand($uid_arr);
                 $user_row=$uid_arr[$uid_row];
                 $order_time=ordertime($year,$mouth,$days);
                 $d=rand(1,$days);
                 $order_sn=$order_time['order_time'].$d.$mouth.str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
                 $oi_row[]=array(
                     'order_sn'=>$order_sn,
                     'user_id'=>$user_row['user_id'],
                     'consignee'=>$user_row['consignee'],
                     'country'=>$user_row['country'],
                     'province'=>$user_row['province'],
                     'city'=>$user_row['city'],
                     'district'=>$user_row['district'],
                     'mobile'=>$user_row['mobile'],
                     'goods_amount'=>number_format($oival['exchange_integral']/100,2),
                     'integral'=>$oival['exchange_integral'],
                     'integral_money'=>number_format($oival['exchange_integral']/100,2),
                     'add_time'=>$order_time['add_time'],
                     'confirm_time'=>$order_time['confirm_time'],
                     'pay_time'=>$order_time['pay_time'],
                     'shipping_time'=>$order_time['shipping_time'],
                     'extension_code'=>'exchange_goods',
                     'extension_id'=>$oival['goods_id'],
                     'goods_name'=>$oival['goods_name'],
                     'goods_sn'=>$oival['goods_sn'],
                     'market_price'=>$oival['market_price'],
                 );
            }
        }
        $res=exchange_add_order($oi_row);
        if($res =='1'){
            var_dump('成功');
            var_dump(date("H:i:s", time() + 8 * 3600));
        }

    }
    $smarty->display('paipai_part_exchange.dwt');

}
elseif($_REQUEST['act'] =='update_exchange'){

    $year=$_POST['year'];
    $mouth = $_POST['mouth'];
    $days=@cal_days_in_month(CAL_GREGORIAN, $mouth, $year);

    $min_time=$year.'-'.$mouth.'-01'.' 00:00:00';
    $max_time=$year.'-'.$mouth.'-'.$days.' 23:59:59';
    $limit_min_time=strtotime($min_time);
    $limit_max_time=strtotime($max_time);
    var_dump(date("H:i:s", time() + 8 * 3600));
    if($year && $mouth){
        $order_sql="SELECT order_id,order_sn,extension_code,extension_id FROM ".$GLOBALS['ecs']->table('order_info')." WHERE add_time<=".$limit_max_time." AND add_time>=".$limit_min_time." AND extension_code='exchange_goods' ORDER BY order_id DESC";
        $update_data=$GLOBALS['db']->getAll($order_sql);
        foreach($update_data as $ukey=>$uval) {
            $order_sn_arr[] = $uval['order_sn'];
        }

        $rec_id_sql = "SELECT rec_id,order_sn,goods_id FROM " . $GLOBALS['ecs']->table('order_goods') . " WHERE order_sn IN (".implode(",", $order_sn_arr).") ORDER BY rec_id DESC";
        $rec_id_row = $GLOBALS['db']->getALL($rec_id_sql);

        $logs_id_sql = "SELECT id,order_sn FROM " . $GLOBALS['ecs']->table('goods_inventory_logs') . " WHERE order_sn IN (".implode(",", $order_sn_arr).") ORDER BY id DESC";
        $logs_id_row = $GLOBALS['db']->getALL($logs_id_sql);

        foreach($update_data as $key=>$val) {
            if($val['order_sn']==$rec_id_row[$key]['order_sn']){
                $update_data[$key]['order_id']=$val['order_id'];
                $update_data[$key]['order_sn']=$rec_id_row[$key]['order_sn'];
                $update_data[$key]['rec_id']=$rec_id_row[$key]['rec_id'];
                $rec_id_arr[] = $rec_id_row[$key]['rec_id'];
            }
            if($val['order_sn']==$logs_id_row[$key]['order_sn']){
                $update_data[$key]['logs_id']=$logs_id_row[$key]['id'];
                $logs_id_arr[] = $logs_id_row[$key]['id'];
            }
        }
        //更改order_info下的order_id
        $up_og_sql="UPDATE  ".$GLOBALS['ecs']->table('order_goods')." SET  order_id= CASE rec_id";
        foreach($update_data as $key4=>$val4) {
            $up_og_sql.=" WHEN ".$val4['rec_id']." THEN ". $val4['order_id'];
        }
        $up_og_sql.=" END WHERE rec_id IN(".implode(",", $rec_id_arr).") ";

        //更改goods_logs下的order_id
        $up_gl_sql="UPDATE  ".$GLOBALS['ecs']->table('goods_inventory_logs')." SET  order_id= CASE id";
        foreach($update_data as $key3=>$val3) {
            $up_gl_sql.=" WHEN ".$val3['logs_id']." THEN ". $val3['order_id'];
        }
        $up_gl_sql.=" END WHERE id IN(".implode(",", $logs_id_arr).") ";

        $og_res=$GLOBALS['db']->query($up_og_sql);
        if($og_res){
            $gl_res=$GLOBALS['db']->query($up_gl_sql);
            if($gl_res){
                var_dump('成功');
                var_dump(date("H:i:s", time() + 8 * 3600));
            }
        }
        if(count($update_data)%1000==0){
            $GLOBALS['db']->query('commit transaction');
            $GLOBALS['db']->query('begin');
        }
        $GLOBALS['db']->query('commit');
    }

    $smarty->display('paipai_part_exchangenext.dwt');

}
elseif($_REQUEST['act'] =='order_delivery'){

    $year=$_POST['year'];
    $mouth = $_POST['mouth'];
//    $days = $_POST['days'];
    $exchange = $_POST['exchange'];
    $days=@cal_days_in_month(CAL_GREGORIAN, $mouth, $year);

    $min_time=$year.'-'.$mouth.'-01'.' 00:00:00';
    $max_time=$year.'-'.$mouth.'-'.$days.' 23:59:59';
    $limit_min_time=strtotime($min_time);
    $limit_max_time=strtotime($max_time);
    var_dump(date("H:i:s", time() + 8 * 3600));
    if($year && $mouth) {
        if($exchange){
            $where.=" AND oi.extension_code='exchange_goods' ";
        }
        $order_sql = "SELECT oi.order_id,oi.order_sn,oi.extension_id,oi.extension_code,oi.user_id,oi.consignee,oi.country,oi.province,oi.city,oi.district,oi.street,oi.mobile,oi.pay_time,og.goods_id,og.goods_sn,og.goods_name FROM " . $GLOBALS['ecs']->table('order_info') . " AS oi LEFT JOIN ".$GLOBALS['ecs']->table('order_goods')." AS og ON oi.order_id=og.order_id WHERE oi.add_time<=" . $limit_max_time . " AND oi.add_time>=" . $limit_min_time .$where. " AND oi.pay_status=2 ORDER BY oi.order_id DESC";
        $order_data = $GLOBALS['db']->getAll($order_sql);
        for($i=0;$i<count($order_data);$i++){
            $invoice_list[$i]='8'.date('dis') . str_pad(mt_rand(1, 99999999999), 11, '0', STR_PAD_LEFT);
        }
        if($order_data){
            //dsc_delivery_order 数据添加
            $do_sql = "INSERT INTO ".$GLOBALS['ecs']->table('delivery_order')." (delivery_sn,order_sn,order_id,invoice_no,add_time,shipping_id,shipping_name,user_id,action_user,consignee,address,country,province,city,district,mobile,update_time,status) VALUES ";
            $dg_sql = "INSERT INTO ".$GLOBALS['ecs']->table('delivery_goods')." (order_id,goods_id,goods_name,goods_sn,is_real,extension_code,send_number) VALUES ";
            foreach($order_data as $key =>$val){
                $order_id_arr[]=$val['order_id'];
                $delivery_sn=$val['order_sn'];
                $invoice_no=$invoice_list[$key];
                $shipping_id='2';
                $shipping_name='圆通速递';
                $add_time=$val['pay_time']+120*55;
                $update_time=$val['pay_time']+7*24*3600;
                $status='0';
                $goods_id=$val['goods_id'];
                $extension_code=$val['extension_code'];
                $goods_name=$val['goods_name'];
                $goods_sn=$val['goods_sn'];
                $is_real='1';
                $send_number='1';
                $order_id=$val['order_id'];
                $action_user='admin';
                $do_sql.= "( '".$delivery_sn."','".$val['order_sn']."','".$order_id."','".$invoice_no."','".$add_time."','".$shipping_id."','".$shipping_name."','".$val['user_id']."','".$action_user."','".$val['consignee']."','".$val['address']."','".$val['country']."','".$val['province']."','".$val['city']."','".$val['district']."','".$val['mobile']."','".$update_time."','".$status."'),";
                $dg_sql .= "( '".$order_id."','".$goods_id."','".$goods_name."','".$goods_sn."','".$is_real."','".$extension_code."','".$send_number."'),";
            }
            $do_sql =substr( $do_sql,0, strlen($do_sql)-1 );
            $dg_sql =substr( $dg_sql,0, strlen($dg_sql)-1 );
            $add_do=$GLOBALS['db']->query($do_sql);
            if($add_do){
                $add_dg=$GLOBALS['db']->query($dg_sql);
            }

            if($add_dg){
                $do_id_sql = "SELECT delivery_id,order_id,invoice_no,add_time FROM " . $GLOBALS['ecs']->table('delivery_order') . " WHERE order_id IN (".implode(",", $order_id_arr).")".'  ORDER BY delivery_id DESC';
                $do_data = $GLOBALS['db']->getAll($do_id_sql);
                $up_dg_sql="UPDATE  ".$GLOBALS['ecs']->table('delivery_goods')." SET  delivery_id= CASE order_id";
                $up_oi_sql="UPDATE  ".$GLOBALS['ecs']->table('order_info')." SET  invoice_no= CASE order_id";
                foreach($do_data as $dkey=>$dval){
                    $up_dg_sql.=" WHEN ".$dval['order_id']." THEN ". $dval['delivery_id'];
                    $up_oi_sql.=" WHEN ".$dval['order_id']." THEN ". $dval['invoice_no'];
                }
                $up_oi_sql.=' END, shipping_time= CASE order_id ' ;
                foreach($do_data as $dkey2=>$dval2){
                    $shipping_time=$dval2['add_time']+2*60;
                    $up_oi_sql.=" WHEN ".$dval2['order_id']." THEN ". $shipping_time;
                }
                $up_oi_sql.=' END, confirm_take_time= CASE order_id ' ;
                foreach($do_data as $dkey3=>$dval3){
                    $confirm_take_time=$dval3['add_time']+15*24*3600;
                    $ntime=time()+8*3600;
                    if($confirm_take_time > $ntime){
                        $confirm_take_time='0';
                    }
                    $up_oi_sql.=" WHEN ".$dval3['order_id']." THEN ". $confirm_take_time;
                }
                $up_oi_sql.=' END, shipping_status= CASE order_id ' ;
                foreach($do_data as $dkey2=>$dval2){
                    $up_oi_sql.=" WHEN ".$dval2['order_id']." THEN ". '2';
                }
                $up_oi_sql.=' END, order_status= CASE order_id ' ;
                foreach($do_data as $dkey2=>$dval2){
                    $up_oi_sql.=" WHEN ".$dval2['order_id']." THEN ". '5';
                }
                $up_dg_sql.=" END WHERE order_id IN(".implode(",", $order_id_arr).") ";
                $up_oi_sql.=" END WHERE order_id IN(".implode(",", $order_id_arr).") ";
                $upd_dg=$GLOBALS['db']->query($up_dg_sql);
                if($upd_dg){
                    $upd_oi=$GLOBALS['db']->query($up_oi_sql);
                    if(count($do_data)%1000==0){
                        $GLOBALS['db']->query('commit transaction');
                        $GLOBALS['db']->query('begin');
                    }
                    if($upd_oi){
                        var_dump('成功');
                        var_dump(date("H:i:s", time() + 8 * 3600));
                    }
                }

            }

        }else{
            var_dump('无数据');
        }
    }

    $smarty->display('paipai_part_orderdelivery.dwt');
}
elseif($_REQUEST['act'] == 'delete_order') {

    $p_start_time=$_POST['start_time'];
    $p_end_time = $_POST['end_time'];
    $extension_code = $_POST['extension_code'];

    $limit_time=strtotime('2018-2-28 23:59:59');
    if($p_start_time && $p_end_time && $extension_code){
        $start_time=strtotime($p_start_time);
        $end_time=strtotime($p_end_time);
        if($start_time < $limit_time){
            var_dump('开始日期必须大于2018年2月');exit;
        }
        if($end_time<=$start_time){
            var_dump('结束日期小于开始日期');exit;
        }
        if($extension_code!='paipai_buy' && $extension_code!='exchange_goods'){
            var_dump('订单类型格式错误');exit;
        }

        $oi_sql="SELECT order_id FROM".$GLOBALS['ecs']->table('order_info')." WHERE extension_code='{$extension_code}' AND add_time>=".$start_time." AND add_time<".$end_time;
        $order_data = $GLOBALS['db']->getAll($oi_sql);
        foreach($order_data as $key=>$val){
            $order_id_arr[]=$val['order_id'];
        }
        $order_id_row=implode(",", $order_id_arr);
        $del_oi='DELETE FROM '.$GLOBALS['ecs']->table('order_info').' WHERE order_id IN('.$order_id_row.')';
        $del_og='DELETE FROM '.$GLOBALS['ecs']->table('order_goods').' WHERE order_id IN('.$order_id_row.')';
        $del_logs='DELETE FROM '.$GLOBALS['ecs']->table('goods_inventory_logs').' WHERE order_id IN('.$order_id_row.')';
        $res1=$GLOBALS['db']->query($del_oi);
        if($res1){
            $res2=$GLOBALS['db']->query($del_og);
            if($res2){
                $res3=$GLOBALS['db']->query($del_logs);
                if($res3){
                    var_dump('删除成功');
                }else{
                    var_dump('出库记录删除失败');
                }
            }else{
                var_dump('订单商品删除失败');
            }
        }else{
            var_dump('订单记录删除失败');
        }

        if(count($order_data)%3000==0){
            $GLOBALS['db']->query('commit');
            $GLOBALS['db']->query('begin');
        }
        $GLOBALS['db']->query('commit');

    }else{
        var_dump('数据不完整');
    }

    $smarty->display('paipai_part_deleteorder.dwt');
}