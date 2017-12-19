<?php
namespace Wechat\Controller;
use Think\Controller;

class IndexController extends Controller {

	/**
	 * 微信 公众号jssdk支付
	 */
	public function jsapi() {
		// 此处根据实际业务情况生成订单 然后拿着订单去支付
		// 用时间戳虚拟一个订单号  （请根据实际业务更改）
		$out_trade_no = time();
		// 组合url
		$url = U('Wechat/Index/pay', array('out_trade_no' => $out_trade_no));
		// 前往支付
		redirect($url);
	}

}