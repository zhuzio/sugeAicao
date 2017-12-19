<?php

namespace Home\Controller;

use Think\Controller;

class TestController extends Controller {

	/**

	 * 生成授权证书 如果收到下载申请则走下载

	 * @return [file] [description]

	 */

	public function certificate() {

		ob_clean();

		$font_url = './Public/font/hwkt.ttf';

		$name = utf8encode('Test');

		$account = 'test';

		$id_number = '7654321';

		$certificate_number = 'P123456';

		$identity = utf8encode("T_联合创始人");

		$zs_url = './Upload/sqzsmbwzp.jpg';

		$tx_url = $this->sqzm_link($account);

		$image = imagecreatefromjpeg($zs_url); // 证书模版图片文件的路径

		$red = imagecolorallocate($image, 00, 00, 00); // 字体颜色

		imageTTFText($image, 60, 0, 900, 1140, $red, $font_url, $name);

		imageTTFText($image, 60, 0, 880, 1310, $red, $font_url, $account);

		imageTTFText($image, 60, 0, 700, 1470, $red, $font_url, $id_number);

		imageTTFText($image, 60, 0, 1700, 1680, $red, $font_url, $identity);

		imageTTFText($image, 60, 0, 730, 2720, $red, $font_url, $certificate_number);

		$src = imagecreatefromstring(file_get_contents($tx_url));

		//获取水印图片的宽高

		list($src_w, $src_h) = getimagesize($tx_url);

		//将水印图片复制到目标图片上，最后个参数100是设置透明度，这里实现不透明效果，170,1100是控制水印坐标位置

		$image_s = imagecopymerge($image, $src, 1660, 2420, 0, 0, $src_w, $src_h, 100);

		$bool = I('get.sqzs_download') ? true : false;

		if ($bool) {

			/* If you want the user to download the file */

			$filename = './Upload/sqzs/' . $account . '.jpg';

			$filename1 = date('YmdHis') . '.jpg';

			header("content-type: image/jpeg");

			header('Content-Disposition: attachment; filename="' . $filename1 . '"'); //指定下载文件的描述

			header('Content-Length:' . filesize($filename)); //指定下载文件的大小

			//将文件内容读取出来并直接输出，以便下载

			readfile($filename);

			imagedestroy($image);

		} else {

			/* if you want to save the file in the web server */

			$path = './';

			$filename = $path . 'test' . '.jpg';

			ImageJpeg($image, $filename);

			imagedestroy($image);

			//$this->assign('filename', $filename);

			//$this->display();

		}

	}

	/**

	 * 授权证明二维码

	 * @param  [type] $account [description]

	 * @return [fielname]          [description]

	 */

	public function sqzm_link($account) {

		$url = U('Home/Register/sqzm/account/' . $account);

		$link = trim($this->server . $url);

		$errorCorrectionLevel = "L"; // 纠错级别：L、M、Q、H

		$matrixPointSize = "9"; // 点的大小：1到10

		$path = './Upload/' . verification . '/';

		$file_name = createQrcode($path, $link, $account, $errorCorrectionLevel, $matrixPointSize);

		return $file_name;

	}
	public function randTest() {
		echo rand(1, 8388);
	}

}