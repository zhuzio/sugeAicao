<?php
function secretPassword($pass) {
	return md5(md5(trim($pass)));
}
function trimPost($post) {
	$arr = [];
	foreach ($post as $key => $value) {
		$arr[$key] = trim($value);
	}
	return $arr;
}
function utf8encode($name) {
	$text_encoding = mb_detect_encoding($name, 'UTF-8, ISO-8859-1');
	# make sure it's in unicode
	if ($text_encoding != 'UTF-8') {
		$name = mb_convert_encoding($name, 'UTF-8', $text_encoding);
	}

	# html numerically-escape everything (&#[dec];)
	$name = mb_encode_numericentity($name,
		array(0x0, 0xffff, 0, 0xffff), 'UTF-8');
	return $name;
}
/*
获取权限树*/
function getAuthTree($obj, $id = 0, &$arr = []) {
	//查询pid为零即顶级分类
	$rows = $obj->where(array('auth_pid' => $id))->select();
	//遍历放arr
	if ($rows) {
		foreach ($rows as $v) {
			$arr[] = $v;
			getAuthTree($obj, $v['auth_id'], $arr);
		}
		return $arr;
	}
}
function bankInfo($card, $bankList) {
	$card_8 = substr($card, 0, 8);
	if (isset($bankList[$card_8])) {
		return $bankList[$card_8];
	}
	$card_6 = substr($card, 0, 6);
	if (isset($bankList[$card_6])) {
		return $bankList[$card_6];
	}
	$card_5 = substr($card, 0, 5);
	if (isset($bankList[$card_5])) {
		return $bankList[$card_5];
	}
	$card_4 = substr($card, 0, 4);
	if (isset($bankList[$card_4])) {
		return $bankList[$card_4];
	}
	return '该卡号信息暂未录入';
}

function isMobile() {
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
		return true;
	}

	//此条摘自TPM智能切换模板引擎，适合TPM开发
	if (isset($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT']) {
		return true;
	}

	//如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if (isset($_SERVER['HTTP_VIA']))
	//找不到为flase,否则为true
	{
		return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
	}

	//判断手机发送的客户端标志,兼容性有待提高
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$clientkeywords = array(
			'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile',
		);
		//从HTTP_USER_AGENT中查找手机浏览器的关键字
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
			return true;
		}
	}
	//协议法，因为有可能不准确，放到最后判断
	if (isset($_SERVER['HTTP_ACCEPT'])) {
		// 如果只支持wml并且不支持html那一定是移动设备
		// 如果支持wml和html但是wml在html之前则是移动设备
		if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
			return true;
		}
	}
	return false;
}
function getStr() {
	//字符串
	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";

	$length = strlen($str) - 1;

	$start = rand(0, $length - 3);

	return substr($str, $start, 3);
}

/*
分页*/
function createPage($count, $recording) {
	$Page = new \Org\Hl\Page($count, $recording); // 实例化分页类 传入总记录数和每页显示的记录数(25)
	return $Page;
}
/*
写分成管理文件*/
function writeSettings($post) {
	$settings = include APP_PATH . 'Common/Conf/settings.php';
	foreach ($settings as $k => $v) {
		if (isset($post[$k])) {
			if (!preg_match("/^\d*$/", $post[$k])) {
				$this->error('参数格式不正确!');
			}
			;
			$settings[$k] = $post[$k];
		}
	}
	return file_put_contents(APP_PATH . 'Common/Conf/settings.php', '<?php return ' . var_export($settings, true) . '; ?>');
}
/*
生成二维码(注册链接)*/
function createQrcode($path, $value, $id, $errorCorrectionLevel, $matrixPointSize) {
	$obj = new \QRcode();
	if ($path != '') {
		$rootPath = $path;
	} else {
		$rootPath = './Qrcode/' . create . '/';
	}
	if (!file_exists($rootPath)) {
		mkdir($rootPath);
	}

	$file_name = $rootPath . $id . '.png';
	if (file_exists($file_name)) {
		unlink($file_name);
	}
	$bool = $obj->png($value, $file_name, $errorCorrectionLevel, $matrixPointSize, 2);
	return $file_name;
}

/*
提供二维码下载*/
function downloadQrcode($value, $errorCorrectionLevel, $matrixPointSize) {
	$obj = new \QRcode();
	$rootPath = './Qrcode/' . 'download' . '/';
	if (!file_exists($rootPath)) {
		mkdir($rootPath);
	}
	$file_name = $rootPath . md5(str_shuffle(mt_rand(0, 999999))) . '.png';
	$download_file_name = md5(str_shuffle(mt_rand(0, 999999))) . '.png';
	$obj->png($value, $file_name, $errorCorrectionLevel, $matrixPointSize, 2);
	try {
		if (file_exists($file_name)) {
			$file = fopen($file_name, "r");
			Header("Content-type: application/octet-stream");
			Header("Accept-Ranges: bytes");
			Header("Accept-Length: " . filesize($file_name));
			Header("Content-Disposition: attachment; filename=" . $download_file_name);
			// 输出文件内容
			echo fread($file, filesize($file_name));
			fclose($file);
			//下载完成后,删除该图片
			unlink($file_name);
		}
	} catch (\Exception $e) {
		echo "无法下载图片\n";
		echo $e->getMessage();
	}
}
/*
发短信
 */
function sendTemplateSMS($to, $datas, $tempId) {
	$serverIP = 'app.cloopen.com';
	$serverPort = '8883';
	$softVersion = '2013-12-26';
	// 初始化REST SDK
	// global $accountSid, $accountToken, $appId, $serverIP, $serverPort, $softVersion;
	$rest = new REST($serverIP, $serverPort, $softVersion);

	$rest->setAccount(C('SID'), C('TOKEN'));
	$rest->setAppId(C('APPID'));

	// 发送模板短信
	echo "Sending TemplateSMS to $to <br/>";
	$result = $rest->sendTemplateSMS($to, $datas, $tempId);
	if ($result == NULL) {
		echo "result error!";
		return false;
	}
	if ($result->statusCode != 0) {
		return false;
	} else {
		return true;
	}

}
