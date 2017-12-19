<?php
namespace Home\Controller;
use Home\Controller\PublicController;

class IndexController extends PublicController {

	public function index() {
		$this->display();
	}
	/**
	 * 生成授权证书 如果收到下载申请则走下载
	 * @return [file] [description]
	 */
	public function certificate() {
		ob_clean();
		$font_url = './Public/font/hwkt.ttf';
		$name = utf8encode($this->memberInfo['name']);
		$account = $this->memberInfo['account'];
		$id_number = $this->memberInfo['id_number'];
		$certificate_number = $this->certificate_no;
		if ($this->memberInfo['is_zd'] == 1 && $this->memberInfo['is_lc'] == 0) {
			$identity = utf8encode("总代理");
		} else {
			$identity = utf8encode("联合创始人");
		}
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
			$path = './Upload/sqzs/';
			$filename = $path . $account . '.jpg';
			ImageJpeg($image, $filename);
			imagedestroy($image);
			$this->assign('filename', $filename);
			$this->display();
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
	/**
	 * 首页判断是否有未读公告
	 * @return boolean [ajax返回状态码code]
	 */
	public function isBrowse() {
		$find = M('Notice_browse')->where(array('browse_id' => session('mid')))->find();
		$notice_ids2 = M('Notice')->field('id')->select();
		//如果未浏览且公告不为空 返回。
		if (empty($find) && !empty($notice_ids2)) {
			$return['code'] = '1';
			$this->ajaxReturn($return);
		}
		//如果未浏览且公告为空 返回。
		if (empty($find) && empty($notice_ids2)) {
			$return['code'] = '2';
			$this->ajaxReturn($return);
		}
		//浏览记录与公告记录想当 则已全部浏览 返回。
		$notice_ids1 = json_decode($find['notice_id']);
		$count1 = count($notice_ids1);
		$count2 = count($notice_ids2);
		if ($count1 != $count2) {
			$return['code'] = '1';
			$return['msg'] = '有未浏览的';
		} else {
			$return['code'] = '2';
			$return['msg'] = '已经全部浏览了';
		}
		$this->ajaxReturn($return);
	}
	/**
	 * 点击公告如果有未读的做已读处理
	 * 若没有已读则返回
	 * @return [type] [description]
	 */
	public function browseAdd() {
		$find = M('Notice_browse')->where(array('browse_id' => session('mid')))->find();
		$notice_ids2 = M('Notice')->field('id')->select();
		//如果浏览记录不为空且公告不为空
		//判断两个是否想当 若是，则已全部浏览 ，返回。
		if (!empty($find) && !empty($notice_ids2)) {
			$notice_ids1 = json_decode($find['notice_id']);
			$count1 = count($notice_ids1);
			$count2 = count($notice_ids2);
			if ($count1 == $count2) {
				$return['msg'] = '已经全部浏览了';
				$this->ajaxReturn($return);
			}
		}
		//浏览记录为空，公告为空 返回
		if (empty($find) && empty($notice_ids2)) {
			$return['code'] = '2';
			$return['msg'] = '没有公告';
			$this->ajaxReturn($return);
		}
		//公告不为空
		if (!empty($notice_ids2)) {
			$ids = [];
			foreach ($notice_ids2 as $key => $value) {
				$ids[] = $value['id'];
			}
			$c_ids = json_encode($ids);
		}
		$find = M('Notice_browse')->where(array('browse_id' => session('mid')))->find();
		//浏览记录为空 公告不为空，则生成浏览记录，否则更新浏览记录
		if ($find['browse_id'] == '' && !empty($notice_ids2)) {
			$data['notice_id'] = $c_ids;
			$data['browse_id'] = session('mid');
			M('Notice_browse')->add($data);
		} else {
			$data['notice_id'] = $c_ids;
			M('Notice_browse')->where(array('browse_id' => session('mid')))->save($data);
		}

	}

}