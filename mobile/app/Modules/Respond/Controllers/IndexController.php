<?php
//zend 多点乐资源  禁止倒卖 一经发现停止任何服务
namespace App\Modules\Respond\Controllers;

class IndexController extends \App\Modules\Base\Controllers\FrontendController
{
	private $data = array();

	public function __construct()
	{
		parent::__construct();
		C('URL_MODEL', 0);
		
		$this->data = array('code' => I('get.code'));

		if (isset($_GET['code'])) {
			unset($_GET['code']);
		}
	}

	public function actionIndex()
	{
 
		$msg_type = 2;
		
		$payment = $this->getPayment();
 
		if ($payment === false) {
			$msg = L('pay_disabled');
		}
		else {
			
			if (isset($_GET['type']) && $this->data['code'] == 'wxpay' && $_GET['type'] == 'wxh5') {
				$log_id = intval($_GET['log_id']);
				$this->redirect('respond/index/wxh5', array('code' => 'wxpay', 'log_id' => $log_id));
			}

			if ($payment->callback($this->data)) {
				$log_id = parse_trade_no($_GET['out_trade_no']);
				$sql1 = 'SELECT * FROM ' . $GLOBALS['ecs']->table('pay_log') . (' WHERE log_id = \'' . $log_id . '\'');
				$pay_log = $GLOBALS['db']->getRow($sql1);
				$sql2 = 'SELECT main_order_id, order_id, user_id, order_sn, ppj_id, ppj_no, extension_code ' . 'FROM ' . $GLOBALS['ecs']->table('order_info') . (' WHERE order_id = \'' . $pay_log['order_id'] . '\'');
				$order_data = $GLOBALS['db']->getRow($sql2);
				if($order_data['extension_code'] == 'paipai_buy'){
//					$this->redirect('user/userbuy/userbuy',array('id'=>$order_data['order_id']));
					$msg="保证金支付成功，进入订单页";
					$msg_type = 0;
					$order_url = url('user/order/indexpaipai',array('status'=>10));
				}else if($order_data['extension_code'] == 'two_price'){
					//$msg = L('pay_success');
					$msg="竞拍成功，进入订单页";
					$msg_type = 0;
					$order_url = url('user/order/indexpaipai',array('status'=>10));
				}else{
					$msg="支付成功，进入订单页";
					$msg_type = 0;
					$order_url = url('user/order/index',array('status'=>0));
				}
				
				
			}
			else {
				$msg = L('pay_fail');
				$msg_type = 1;
			}
		}
		
		if (isset($_GET['log_id']) && !empty($_GET['log_id'])) {
			
			$log_id = intval($_GET['log_id']);
			
			$pay_log = dao('pay_log')->field('order_type, order_id')->where(array('log_id' => $log_id))->find();

			if ($pay_log['order_type'] == 0) {
				
				$order_url = url('user/order/detail', array('order_id' => $pay_log['order_id']));
				
			}
			else if ($pay_log['order_type'] == 1) {
				$order_url = url('user/account/detail');
			}
			else if ($pay_log['order_type'] == 2) {
				$order_url = url('drp/user/index');
			}
			else if ($pay_log['order_type'] == 3) {
				$order_url = url('team/user/index');
			}
			
		}


		$order_url = str_replace('respond', 'index', $order_url);
		
		$this->assign('payment', $this->data['total_amount']);
		
		$this->assign('message', $msg);
		$this->assign('msg_type', $msg_type);
		$this->assign('order_url', $order_url);
		$this->assign('page_title', L('pay_status'));
		$this->display();
	}


	public function actionNotify()
	{
		$payment = $this->getPayment();

		if ($payment === false) {
			exit('plugin load fail');
		}

		$payment->notify($this->data);
	}

	private function getPayment()
	{
		$condition = array('pay_code' => $this->data['code'], 'enabled' => 1);
		$enabled = $this->db->table('payment')->where($condition)->count();
		$plugin = ADDONS_PATH . 'payment/' . $this->data['code'] . '.php';
		if (!is_file($plugin) || $enabled == 0) {
			return false;
		}

		require_cache($plugin);
		$payment = new $this->data['code']();
		return $payment;
	}

	public function actionWxh5()
	{
		if (isset($_GET) && !empty($_GET['log_id'])) {
			$log_id = intval($_GET['log_id']);
			$pay_log = dao('pay_log')->field('order_type, order_id')->where(array('log_id' => $log_id))->find();

			if ($pay_log['order_type'] == 0) {
				$order_url = url('user/order/detail', array('order_id' => $pay_log['order_id']));
			}
			else if ($pay_log['order_type'] == 1) {
				$order_url = url('user/account/detail');
			}
			else if ($pay_log['order_type'] == 2) {
				$order_url = url('drp/user/index');
			}
			else if ($pay_log['order_type'] == 3) {
				$order_url = url('team/user/index');
			}

			$order_url = str_replace('respond', 'index', $order_url);
			$repond_url = __URL__ . '/respond.php?code=' . $this->data['code'] . '&status=1&log_id=' . $log_id;
		}
		else {
			$repond_url = __URL__ . '/respond.php?code=' . $this->data['code'] . '&status=0';
		}

		$is_wxh5 = $this->data['code'] == 'wxpay' && !is_wechat_browser() ? 1 : 0;
		$this->assign('is_wxh5', $is_wxh5);
		$this->assign('repond_url', $repond_url);
		$this->assign('order_url', $order_url);
		$this->assign('page_title', '确认支付');
		$this->display();
	}
}

?>
