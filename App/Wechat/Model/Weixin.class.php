<?php
namespace Wechat\Model;

class Weixin {
	public function __construct() {
		Vendor('Weixinpay.Weixinpay');
		Vendor('phpqrcode.phpqrcode');
	}
	public function weixinpay($order) {
		$order['trade_type'] = 'NATIVE';
		$weixinpay = new \Weixinpay();
		$url = $weixinpay->pay($order);
		return $this->qrcode($url);
	}
	public function qrcode($url, $size = 6) {
		$date = date('Y-m-d', time());
		$rootPath = './Qrcode/' . $date . '/';
		if (!file_exists($rootPath)) {
			mkdir($rootPath);
		}
		\QRcode::png($url, false, QR_ECLEVEL_L, $size, 2, false, 0xFFFFFF, 0x000000);
		//$filename = $rootPath . md5($url . '|' . QR_ECLEVEL_L . '|' . $size) . '.png';
		$filename = $rootPath . '1' . '.png';
		//\QRcode::png($url, $filename, QR_ECLEVEL_L, $size, 2);
		//return $filename;
	}
}