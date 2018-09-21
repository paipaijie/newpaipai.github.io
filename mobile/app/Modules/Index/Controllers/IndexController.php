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
		//查询商品信息
		$sql = 'SELECT b.*, IFNULL(g.goods_thumb, \'\') AS goods_thumb, b.ppj_id AS group_buy_id, g.market_price,' . 'b.start_time AS start_date, b.end_time AS end_date,g.goods_number ' . 'FROM ' . $GLOBALS['ecs']->table('paipai_list') . ' AS b ' . 'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON b.goods_id = g.goods_id ' . 'WHERE b.act_type = \'' . GAT_PAIPAI_BUY . ('\' ' . $where . ' ') . (' AND b.ppj_staus < 3 AND b.review_status = 3 ORDER BY ppj_id DESC');
		$res = $GLOBALS['db']->query($sql);
		$arr = array();
		// var_dump($res);
		$now = time();
		foreach($res as $k => $val){
			$val['goods_thumb']  = __STATIC__ .'/'. $val['goods_thumb'];
			$arr[] = $val['goods_thumb'];
			if($val['end_time'] > $now && $val['start_time'] < $now){
				$val['is_end'] = 1;
			}else if($val['start_time'] > $now){
				$val['is_end'] = 0;
			}else if($val['end_time']<$now){
				$val['is_end'] = 3;
			}
			$arr3[] = $val['is_end'];

			$sql1 = "select sum(goods_number) from dsc_order_goods where ppj_no = {$val['ppj_no']} and goods_id = {$val['goods_id']}";
			$r = $GLOBALS['db']->getAll($sql1);

			$strlen = strlen($res[$k]['goods_name']);
			$res[$k]['goods_name'] = $strlen>20?mb_substr($res[$k]['goods_name'],0,12,'utf-8'):$res[$k]['goods_name'];

			foreach($r as $k => $v){
				//var_dump($v);
				$res[$k]['order_number'] = $v['sum(goods_number)'];
			}
			$val['end_date']=floor(($val['end_time']-time())/86400);
			if($val['end_date']>0){
				$val['end_date']=floor(($val['end_time']-time())/86400);
			}else{
				$countdown = ($val['end_date'] - $now)-8*60*60;
				$date_count = date('H:i:s',$countdown);
				$val['end_date'] = $date_count;
			}
			$arr2[] = $val['end_date'];

			// $val['end_ti'] = $val['end_time']-$now;
		
			// $arr2[] = $val['end_ti'];
			// 	// $res[$k]['end_time_daojishi'] = $val['end_date'];
		
			
			$sql = "select count(*) from dsc_order_info where ppj_id ={$val['ppj_id']} and ppj_no = {$val['ppj_no']} and pay_status != 11";
			$result = $GLOBALS['db']->query($sql);
			foreach($result as $ke => $ek){
				//var_dump($ek);
				$b[] = $ek['count(*)'];
			}
		}
		
		foreach($arr as $k=>$v){
			$res[$k]['goods_thumb'] = $v;
		}
		foreach($b as $k=>$v){
			$res[$k]['count'] = $v;
		}
		
		foreach($arr2 as $k=>$v){
			$res[$k]['end_ti'] = $v;
		}
		foreach($arr3 as $k=>$v){
			$res[$k]['is_end'] = $v;
		}
		$this->assign('res', $res);	
		$this->display();
	}

	public function actionAppNav()
	{
		$app = C('shop.wap_index_pro') ? 1 : 0;
		$this->response(array('error' => 0, 'data' => $app));
	}


	public function actionNotice()
	{
		$condition = array('is_open' => 1, 'cat_id' => 12);
		$list = $this->db->table('article')->field('article_id, title, author, add_time, file_url, open_type')->where($condition)->order('article_type DESC, article_id DESC')->limit(5)->select();
		$res = array();

		foreach ($list as $key => $vo) {
			$res[$key]['text'] = $vo['title'];
			$res[$key]['url'] = build_uri('article', array('aid' => $vo['article_id']));
		}

		$this->response(array('error' => 0, 'data' => $res));
	}

	public function actionGoods()
	{
		$number = input('post.number', 10);		
		$condition = array('intro' => input('post.type', ''));	
		$list = $this->getGoodsList($condition, $number);
		
		$res = array();
		$endtime = gmtime();

		foreach ($list as $key => $vo) {
			$res[$key]['desc'] = $vo['name'];
			$res[$key]['sale'] = $vo['sales_volume'];
			$res[$key]['stock'] = $vo['goods_number'];

			if ($vo['promote_price']) {
				$res[$key]['price'] = min($vo['promote_price'], $vo['shop_price']);
			}
			else {
				$res[$key]['price'] = $vo['shop_price'];
			}

			$res[$key]['marketPrice'] = $vo['market_price'];
			$res[$key]['img'] = $vo['goods_thumb'];
			$res[$key]['link'] = $vo['url'];
			$endtime = $endtime < $vo['promote_end_date'] ? $vo['promote_end_date'] : $endtime;
		}

		$this->response(array('error' => 0, 'data' => $res, 'endtime' => date('Y-m-d H:i:s', $endtime)));
	}

	public function actionSpa()
	{
		$this->display();
	}

	private function getGoodsList($param = array(), $size = 10)
	{
		$data = array('id' => 0, 'brand' => 0, 'intro' => '', 'price_min' => 0, 'price_max' => 0, 'filter_attr' => 0, 'sort' => 'goods_id', 'order' => 'desc', 'keyword' => '', 'isself' => 0, 'hasgoods' => 0, 'promotion' => 0, 'page' => 1, 'type' => 1, 'size' => $size, C('VAR_AJAX_SUBMIT') => 1);
		
		
		$data = array_merge($data, $param);
		
		$cache_id = md5(serialize($data));
		
		$list = S($cache_id);

		if ($list === false) {
			
			$url = url('category/index/products', $data, false, true);
			$res = \App\Extensions\Http::doGet($url);

			if ($res === false) {
				$res = file_get_contents($url);
			}

			if ($res) {
				$data = json_decode($res, 1);
				$list = empty($data['list']) ? false : $data['list'];
				S($cache_id, $list, 600);
			}
		}

		return $list;
	}
}

?>
