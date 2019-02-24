<?php
//多点乐资源
namespace App\Modules\Mapi\Controllers;

class IndexController extends \App\Modules\Base\Controllers\FrontendController
{
	private $index_banner='256';  //首页banner


	public function actionIndex()
	{
		//分类
		$sort_sql='SELECT cat_id,cat_name,parent_id FROM '. $GLOBALS['ecs']->table('category') . ' WHERE parent_id=0 AND is_show=1 ' ;
		$main_sort=$GLOBALS['db']->getAll($sort_sql);

		//banners
		$banners_sql='SELECT ad_id,position_id,ad_name,ad_link,ad_code FROM '. $GLOBALS['ecs']->table('touch_ad') .
			' WHERE position_id='.$this->index_banner ;
		$index_banners=$GLOBALS['db']->getAll($banners_sql);



		$this->resp(array('sort' => $main_sort,'banner'=>$index_banners));

	}


}

?>