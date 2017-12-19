<?php
namespace Wechat\Controller;
use Think\Controller;
use Wechat\Model\Weixin;

class IndexController extends Controller {
	protected $weixin;
	public function __construct() {
		$this->weixin = new Weixin();
	}
	/**
	 * 微信 扫描支付
	 */
	public function index() {
		// 此处根据实际业务情况生成订单 然后拿着订单去支付
		// 虚拟的订单 请根据实际业务更改
		$time = time();
		$order = array(
			'body' => 'test',
			'total_fee' => 1,
			'out_trade_no' => strval($time),
			'product_id' => 1,
		);
		$this->weixin->weixinpay($order);
	}
	/**
	 * notify_url接收页面
	 */
	public function notify() {
		// 导入微信支付sdk
		Vendor('Weixinpay.Weixinpay');
		$wxpay = new \Weixinpay();
		$result = $wxpay->notify();
		$data = array(
			'is_paid' => 1,
		);
		if ($result) {
			M('Dian2')->where(array('id' => 2))->save($data);
			// 验证成功 修改数据库的订单状态等 $result['out_trade_no']为订单号
		}
	}
	/**
	 * notify_url接收页面
	 */
	public function notify1() {
		// 导入微信支付sdk
		Vendor('Weixinpay.Weixinpay');
		$wxpay = new \Weixinpay();
		$result = $wxpay->notify();
		if ($result) {
			// 验证成功 修改数据库的订单状态等 $result['out_trade_no']为订单号
		}
	}

	/**
	 * 公众号支付 必须以get形式传递 out_trade_no 参数
	 * 示例请看 /Application/Home/Controller/IndexController.class.php
	 * 中的weixinpay_js方法
	 */
	public function pay() {
		// 导入微信支付sdk
		Vendor('Weixinpay.Weixinpay');
		$wxpay = new \Weixinpay();
		// 获取jssdk需要用到的数据
		$data = $wxpay->getParameters();
		// 将数据分配到前台页面
		$assign = array(
			'data' => json_encode($data),
		);
		print_r($assign);die;
		$this->assign($assign);
		$this->display();
	}
	public function product() {
		$this->display();
	}
	public function Inquire() {
		$pay = M('Dian2')->where(array('id' => 2))->getField('is_paid');
		if ($pay == 1) {
			$data1['code'] = 1;
			M('Dian2')->where(array('id' => 2))->save(array('is_paid' => 0));
		} else {
			$data1['code'] = -1;
		}
		$this->ajaxreturn($data1);
	}
}