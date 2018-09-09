<?php
//zend by 多点乐  禁止倒卖 一经发现停止任何服务
namespace App\Modules\Index\Controllers;

class IndexController extends \App\Modules\Base\Controllers\FrontendController
{
	public function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		header('Access-Control-Allow-Headers: X-HTTP-Method-Override, Content-Type, x-requested-with, Authorization');
	}
	
	

	public function actionIndex()
	{
		
		
		 $this->display();
	}

	public function actionAppNav()
	{
		
	}

	public function actionNotice()
	{
		
	}

	public function actionGoods()
	{
		
	}

	public function actionSpa()
	{
		$this->display();
	}

	
}

?>
