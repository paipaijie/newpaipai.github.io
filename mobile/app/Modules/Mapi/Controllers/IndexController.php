<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 17:08
 */
namespace App\Modules\Mapi\Controllers;

class IndexController extends \App\Modules\Mapi\Foundation\Controller
{

    //顶部（信息，分类）
    public function actionTop()
    {
        //信息
        $message=0;
        //分类
        $sort_sql='SELECT cat_id,cat_name FROM '. $GLOBALS['ecs']->table('category') . ' WHERE parent_id=0 AND is_show=1 ' ;
        $main_sort=$GLOBALS['db']->getAll($sort_sql);
        $persale_arr=array('cat_id'=>'1','cat_name'=>'预售');
        $main_sort[]=$persale_arr;
        foreach($main_sort as $key=>$val){
            $main_sort[$key]['url']='http://www.paipaistreet.com/mapi/index/index?cat_id='.$val['cat_id'];
        }
        $main_sort_data=array(
            'code'=> 10001,
            'data'=>$main_sort
        );
        $message_data=array(
            'code'=> 10001,
            'data'=> $message
        );
        $this->resp(array('sort' => $main_sort_data,'message'=>$message_data));
    }


    //活动
	public function actionIndex()
	{

        $cat_id=$_POST['cat_id'];
        if(empty($cat_id) || $cat_id=='1'){
            $ap_sql='SELECT position_id,ad_width FROM '. $GLOBALS['ecs']->table('touch_ad_position') .' WHERE position_desc=1';
            $ap_data=$GLOBALS['db']->getAll($ap_sql);
        }else{
            $cat_one_sql="SELECT cat_id,cat_name FROM ".$GLOBALS['ecs']->table('category') . ' WHERE cat_id='.$cat_id .' AND parent_id=0  AND is_show=1 ' ;
            $cat_one_data=$GLOBALS['db']->getRow($cat_one_sql);
            if($cat_id =='' || empty($cat_one_data)){
                $sort_data=array(
                    'code'=> 10003,
                    'data'=>'分类参数无效'
                );
                $this->resp(array('cat_data'=>$sort_data));
            }else{
                $ap_sql='SELECT position_id,ad_width FROM '. $GLOBALS['ecs']->table('touch_ad_position') .' WHERE position_desc='.$cat_id;
                $ap_data=$GLOBALS['db']->getAll($ap_sql);
            }
        }

        foreach($ap_data as $apkey=>$apval){
            if($apval['ad_width']<200){
                $module_id=$apval['position_id'];   //模块图
            }elseif($apval['ad_width']>200){
                $banner_id=$apval['position_id'];     //banner
            }
        }
        if(!$module_id || !$banner_id){
            $sort_data=array(
                'code'=> 10003,
                'data'=>'参数不足,无效数据'
            );
            $this->resp(array('cat_data'=>$sort_data));
        }

		//banners
		$banners_sql='SELECT ad_id,position_id,ad_name,ad_link,ad_code FROM '. $GLOBALS['ecs']->table('touch_ad') .' WHERE position_id='.$banner_id ;
		$index_banners_data=$GLOBALS['db']->getAll($banners_sql);
        if($index_banners_data){
            foreach($index_banners_data as $bkey=>$bval){
                $index_banners_data[$bkey]['ad_code']='http://www.paipaistreet.com/data/afficheimg/'.$bval['ad_code'];
            }
            $index_banners=array(
                'code'=> 10001,
                'data'=>$index_banners_data
            );
        }else{
            $index_banners=array(
                'code'=> 10002,
                'data'=>'暂无数据'
            );
        }

        //二级目录
        if(!$module_id){
            $cat_icon_date=array(
                'code'=> 10002,
                'data'=>'暂无数据'
            );
        }else{
            $icon_sql='SELECT ta.ad_id,ta.ad_name,ta.ad_link,ta.ad_code FROM '. $GLOBALS['ecs']->table('touch_ad_position') . 'AS tap LEFT JOIN '.$GLOBALS['ecs']->table('touch_ad').'AS ta ON tap.position_id=ta.position_id WHERE tap.position_id='.$module_id.' ORDER BY ta.ad_id ASC' ;
            $icon_data=$GLOBALS['db']->getAll($icon_sql);
            if($icon_data){
                foreach($icon_data as $ikey=>$ival){
                    $icon_data[$ikey]['ad_code']='http://www.paipaistreet.com/data/afficheimg/'.$ival['ad_code'];
                }
                $cat_icon_date=array(
                    'code'=> 10001,
                    'data'=>$icon_data
                );
            }else{
                $cat_icon_date=array(
                    'code'=> 10002,
                    'data'=>'暂无数据'
                );
            }
        }

        //拍拍活动（ALL）
        $ntime=time()+8*3600;
        $ndays=date('Y-m-d',$ntime);

        if($cat_id && $cat_id!=1){
            $cat_all_sql="SELECT cat_id FROM ".$GLOBALS['ecs']->table('category') . ' WHERE parent_id='.$cat_id .' AND is_show=1 ' ;
            $cat_all_data=$GLOBALS['db']->getAll($cat_all_sql);
            foreach($cat_all_data as $ckey=>$cval){
                $cat_id_arr[]=$cval['cat_id'];
            }
            $cat_id_row=implode(",", $cat_id_arr);
            $where.=' AND g.cat_id IN ('.$cat_id_row.') ';
        }


        //前一天活动
        $old_limit_min_time=strtotime($ndays.' 00:00:01')-24*3600;
        $old_limit_max_time=strtotime($ndays.' 23:59:59')-24*3600;
        $old_pai_sql='SELECT pl.ppj_id,pl.ppj_name,pl.goods_id,pl.ppj_no,pl.goods_count,pl.ppj_now_fee,pl.start_time,pl.end_time,pl.ppj_sale_time,pl.ext_info,pl.ppj_staus,g.goods_thumb
                      FROM '.$GLOBALS['ecs']->table('paipai_list').' AS pl LEFT JOIN '.$GLOBALS['ecs']->table('goods').' AS g ON pl.goods_id=g.goods_id
                      WHERE pl.end_time <='. $old_limit_max_time .' AND pl.start_time >= '.  $old_limit_min_time.$where.' ORDER BY pl.ppj_id DESC LIMIT 10';
        $old_paipai_row=$GLOBALS['db']->getAll($old_pai_sql);

        //后一天活动
        $next_limit_min_time=strtotime($ndays.' 00:00:01')+24*3600;
        $next_limit_max_time=strtotime($ndays.' 23:59:59')+24*3600;
        $next_pai_sql='SELECT pl.ppj_id,pl.ppj_name,pl.goods_id,pl.ppj_no,pl.goods_count,pl.ppj_now_fee,pl.start_time,pl.end_time,pl.ppj_sale_time,pl.ext_info,pl.ppj_staus,g.goods_thumb
                      FROM '.$GLOBALS['ecs']->table('paipai_list').' AS pl LEFT JOIN '.$GLOBALS['ecs']->table('goods').' AS g ON pl.goods_id=g.goods_id
                      WHERE pl.end_time <='. $next_limit_max_time .' AND pl.start_time >= '.  $next_limit_min_time.$where.' ORDER BY pl.ppj_id ASC LIMIT 10';
        $next_paipai_row=$GLOBALS['db']->getAll($next_pai_sql);

        //当日活动
        $limit_min_time=strtotime($ndays.' 00:00:01');
        $limit_max_time=strtotime($ndays.' 23:59:59');
        $now_pai_sql='SELECT pl.ppj_id,pl.ppj_name,pl.goods_id,pl.ppj_no,pl.goods_count,pl.ppj_now_fee,pl.start_time,pl.end_time,pl.ppj_sale_time,pl.ext_info,pl.ppj_staus,g.goods_thumb
                      FROM '.$GLOBALS['ecs']->table('paipai_list').' AS pl LEFT JOIN '.$GLOBALS['ecs']->table('goods').' AS g ON pl.goods_id=g.goods_id
                      WHERE pl.end_time <='. $limit_max_time .' AND pl.start_time >= '.  $limit_min_time.$where;
        $now_paipai_row=$GLOBALS['db']->getAll($now_pai_sql);
        $paipai_row=array_merge($old_paipai_row,$now_paipai_row,$next_paipai_row);
        if($paipai_row){
            foreach($paipai_row as $key=>$val){
                //当前活动参与人数
                $pay_margin_amount_sql='SELECT count(ls_pay_ok) as amount FROM '.$GLOBALS['ecs']->table('paipai_seller_pay_margin').' WHERE ppj_id='.$val['ppj_id'].' AND ppj_no='.$val['ppj_no'].' AND ls_pay_ok=1';
                $pay_margin_amount=$GLOBALS['db']->getOne($pay_margin_amount_sql);
                $ext_info = unserialize($val['ext_info']);
                $val = array_merge($val, $ext_info);
                $price_ladder = $val['price_ladder'];
                foreach($price_ladder as $key2=>$val2){
                    if ($val2['amount'] <= $pay_margin_amount) {
                        $cur_price = $val2['price'];
                    }else if( $pay_margin_amount == 0 ) {
                        $cur_price=0;break;
                    }
                }
                if($val['end_time']<= $limit_min_time ){
                    $pai_row[$key]['sort_status']='end';
                }else if($val['start_time'] >= $limit_max_time){
                    $pai_row[$key]['sort_status']='nostart';
                }else{
                    $pai_row[$key]['sort_status']='ing';
                    $order_amount_sql=' SELECT COUNT(order_id) FROM '.$GLOBALS['ecs']->table('order_info').' WHERE ppj_id='.$val['ppj_id'].' AND ppj_no='.$val['ppj_no'];
                    $order_amount=$GLOBALS['db']->getRow($order_amount_sql);
                    if($order_amount==$val['goods_count']){
                        $pai_row[$key]['sort_status']='end';
                    }else{
                        $pai_row[$key]['sort_status']='ing';
                    }
                }
                $pai_row[$key]['ppj_name']=$val['ppj_name'];
                $pai_row[$key]['goods_thumb']='http://www.paipaistreet.com'.get_image_path($val['goods_thumb']);
                $pai_row[$key]['ppj_id']=$val['ppj_id'];
                $pai_row[$key]['ppj_no']=$val['ppj_no'];
                $pai_row[$key]['start_time']=date("Y-m-d H:i:s",$val['start_time']);
                $pai_row[$key]['end_time']=date("Y-m-d H:i:s",$val['end_time']);
                $pai_row[$key]['ppj_status']=$val['ppj_staus'];
                $pai_row[$key]['ppj_sale_time']=$val['ppj_sale_time'];
                $pai_row[$key]['pay_amount']=$pay_margin_amount;
                $pai_row[$key]['now_price']=price_format($cur_price,false);
                $pai_row[$key]['url'] = 'http://www.paipaistreet.com/mapi/pai/details?pid='.$val['ppj_id'];
            }
            $new_pai_row=array();
            foreach($pai_row as $pkey=>$pval){
                if($pval['sort_status']=='end'){
                    $new_pai_row['end'][]=$pval;
                }elseif($pval['sort_status']=='ing'){
                    $new_pai_row['ing'][]=$pval;
                }else{
                    $new_pai_row['nostart'][]=$pval;
                }
            }
            $pai_data=array(
                'code'=> 10001,
                'data'=> $new_pai_row
            );
        }else{
            $pai_data=array(
                'code'=> 10002,
                'data'=>'暂无数据'
            );
        }

        $this->resp(array('banner'=>$index_banners,'sort_icon'=>$cat_icon_date,'pai_data'=>$pai_data));

	}

}

?>