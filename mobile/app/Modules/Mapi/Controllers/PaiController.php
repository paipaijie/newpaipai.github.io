<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/23
 * Time: 17:08
 */
namespace App\Modules\Mapi\Controllers;

class PaiController extends \App\Modules\Mapi\Foundation\Controller
{

    //拍拍详情
    public function actionDetails(){

        //$ppj_id=$_REQUEST['ppj_id'];

        $this->resp(array('sort' =>'1'));
    }


}

?>