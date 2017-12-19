<?php
namespace Home\Controller;
use Home\Controller\PublicController;

class RegisterController extends PublicController {
	/*
	注册链接*/
	public function shareLink() {
		/*外部链接*/
		$str = getStr() . session('account');
		$account = base64_encode($str);
		$url = U('Register/register?account=' . $account);
		$name = M('Member')->where(array('account' => $account))->getField('name');
		$link = trim($this->server . $url);
		$errorCorrectionLevel = "L"; // 纠错级别：L、M、Q、H
		$matrixPointSize = "9"; // 点的大小：1到10
		$file_name = createQrcode('', $link, session('mid'), $errorCorrectionLevel, $matrixPointSize);
		$assign = array(
			'file_name' => $file_name,
			'link' => $link,
			'name' => $name,
			'account' => session('account'),
		);
		$this->assign($assign);
		$this->display();
	}
	public function registerAction1() {
		if (!IS_POST) {
			$this->error('非法操作！');
		}
		$post = I('post.');
		$account = base64_decode($post['account']);
		$return = D('Member')->check_account($account);
		if ($return['code'] == '-1') {
			$this->error($return['msg']);
		}
		$return1 = $this->memberModel->is_phone_exist($post['phone']);
		/*
				检查手机号
			*/
		if ($return1['code'] == '-1') {
			$this->error($return['msg']);
		}
		if (trim($post['pass'] != trim($post['pass2']))) {
			$this->error('两次输入密码不一致！');
		}
		$arr = D('Member')->getInfo(session('mid'));
		$data['recommend_lc'] = $arr['is_lc'] == 1 ? session('account') : $arr['recommend_lc'];
		$data['recommend_id'] = session('mid');
		$data['recommend_account'] = session('account');
		$data['id_number'] = session('account');
		$data['pass'] = $post['pass'];
		$data['account'] = trim($post['account']);
		$data['m_phone'] = trim($post['phone']);
		$data['reg_time'] = time();
		M('Member')->add($data);

	}

	/*
	外部注册链接*/
	public function register() {
		$account = substr(base64_decode(I('get.account')), 3);
		$name = M('Member')->where(array('account' => $account))->getField('name');
		$assign = array(
			'name' => $name,
			'account' => $account,
		);
		$this->assign($assign);
		$this->display();
	}
	/**
	 * 接口请求，发送验证码
	 * @return [type] [description]
	 */
	public function sendSmsCode() {
		$phone = trim(I('post.phone'));
		$return = D('Member')->is_phone_exist($phone);
		if ($return['code'] == '-1') {
			$this->ajaxReturn($return);
		}
		$bool = D('Member')->getSmsCode($phone);
		$data['code'] = $bool ? 1 : -1;
		$this->ajaxReturn($data);
	}
	/*
    	注册*/
	/*
		public function regwai() {
		    $this->display();
		}
	*/
	public function registerAction() {
		if (!IS_POST) {
			$this->error('非法操作！');
		}
		$post = trimPost(I('post.'));
		$find = M('Member')->where(array('account' => $post['account']))->find();
		if (!empty($find)) {
			die("<script>alert('账号已存在!');history.back(-1);</script>");
		}

		if ($post['pass'] != $post['pass2']) {
			die("<script>alert('两次输入密码不一致！');history.back(-1);</script>");
		}
		$sms_code = session('sms_code' . $post['phone']);
		if ($post['code'] != $sms_code) {
			die("<script>alert('验证码错误！');history.back(-1);</script>");
		}
		$r_account = $post['recommend_account'];
		$arr = M('Member')->where(array('account' => $r_account))->find();
		if ($arr['is_lc'] == 1) {
			$data['recommend_lc'] = $arr['account'];
		} else {
			$data['recommend_lc'] = $arr['recommend_lc'];
		}
		$data['recommend_id'] = $arr['id'];
		$data['recommend_account'] = $arr['account'];
		$data['id_number'] = $post['id_number'];
		$data['name'] = $post['name'];
		$data['pass'] = secretPassword($post['pass']);
		$data['account'] = $post['account'];
		$data['m_phone'] = $post['phone'];
		$data['reg_time'] = time();
		$newid = M('Member')->add($data);
		$data1['s_id'] = $newid;
		$data1['s_account'] = $post['account'];
		$data1['s_phone'] = $post['phone'];
		$data1['s_consignee'] = $post['name'];
		$data1['s_addr'] = $post['address'];
		$data1['status'] = 1;
		M('Address')->add($data1);
		$url = $this->server . U('Login/index');
		die("<script>alert('注册成功请等待联合创始人审核！');window.location.href='$url'</script>");
	}
	public function is_account_exist() {
		$account = I('post.account');
		$arr = M('Member')->where(array('account' => $account))->find();
		$data['code'] = !empty($arr) ? 1 : -1;
		$this->ajaxReturn($data);
	}
	/**
	 * 授权证明
	 * @return [array] [arr 个人信息 certificate_no 证书编号]
	 */
	public function sqzm() {
		$arr = M('Member')->where(array('account' => I('get.account')))->find();
		$certificate = M('Certificate')->where(array('account' => I('get.account')))->find();
		$certificate_no = $certificate['certificate_no'];
		$assign = array('memberInfo' => $arr, 'certificate_no' => $certificate_no);
		$this->assign($assign);
		$this->display();
	}
	public function zabxq() {
		$this->display();
	}
	public function zabxq1() {
		$this->display();
	}
}