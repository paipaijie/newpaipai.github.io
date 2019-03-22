<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/27 0027
 * Time: 15:17
 */
//  
//  is_finished :
//			未开始为0
//			进行中为1
//			活动结束为2
//			活动成功为3
//			活动失败为4
//   ppj_staus
//         0表示未开始，1表示已开始，2表示已结束
//			*/
function paipai_active_update(){
      $ntime=time()+8*3600;

      $sql="SELECT ppj_id,start_time,end_time,ppj_staus,is_finished,ppj_status_end_time FROM ".$GLOBALS['ecs']->table('paipai_list')." WHERE ppj_staus!=2";
      $res=$GLOBALS['db']->getAll($sql);

      foreach($res as $key=>$val){

          //ppj_staus为0时无法结束活动 为1时结束活动操作退款并更改为2
          if($val['ppj_staus']==0){
              if($val['end_time']<$ntime){    // ppj_staus 改为1
                  $up_data=array('ppj_staus'=>'1');
              }else if($val['start_time']<$ntime && $ntime<$val['end_time']){   // ppj_staus 改为1
                  $up_data=array('ppj_staus'=>'1');
              }
              $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('paipai_list'), $up_data,'UPDATE','ppj_id='.$val['ppj_id']);
          }


      }



}