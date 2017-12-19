<?php
/**
 * Created by h l.
 * User: huanglei
 * Date: 2017/6/10 11:57
 */
header("content-type:text/html;charset=utf-8");
/**
 * 生成二维码（支付）
 * @param  string  $url  url连接
 * @param  integer $size 尺寸 纯数字
 */
function qrcode($url, $size = 6) {
	Vendor('phpqrcode.phpqrcode');
	$date = date('Y-m-d', time());
	$rootPath = './Qrcode/' . $date . '/';
	if (!file_exists($rootPath)) {
		mkdir($rootPath);
	}
	//QRcode::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
	$filename = $rootPath . md5($url . '|' . QR_ECLEVEL_L . '|' . $size) . '.png';
	QRcode::png($url, $filename, QR_ECLEVEL_L, $size, 2);
	return $filename;
}