<?php
namespace Home\Controller;

class LoginController extends PublicController {
	public function index() {
		//sendTemplateSMS('13193835328', array('123456', '5分钟'), '196824');
		$this->display('login');
	}
	public function loginAct() {
		$obj = M('Member');
		/*
		用户名或密码是否正确*/
		$where['account'] = trim(I('post.account'));
		$where['pass'] = secretPassword(I('post.pass'));
		$where['status'] = array('eq', 1);
		//print_r($where);die;
		$arr = $obj->where($where)->find();
		//print_r($arr);die;
		if (empty($arr)) {
			die("<script>alert('用户名或密码不正确!');history.back(-1);</script>");
		}
		/*
		用户名或密码是否正确*/
		$where['account'] = trim(I('post.account'));
		$where['pass'] = secretPassword(I('post.pass'));
		$where['delete_time'] = array('gt', 0);
		$arr = $obj->where($where)->find();
		if (!empty($arr)) {
			die("<script>alert('用户不存在!');history.back(-1);</script>");
		}
		/*
		查看是否被禁用*/

		$where1['account'] = trim(I('post.account'));
		$where1['pass'] = secretPassword(I('post.pass'));
		$where1['status'] = array('eq', 0);
		$arr = $obj->where($where1)->find();
		if (!empty($arr)) {
			die("<script>alert('您已被禁用！');history.back(-1);</script>");
		}
		/*
		查看是否经过审核*/
		$where2['account'] = trim(I('post.account'));
		$where2['pass'] = secretPassword(I('post.pass'));
		$where2['status'] = array('eq', 1);
		$where2['refuse_time'] = array('gt', 0);
		$where2['update_time'] = array('gt', 0);
		$arr = $obj->where($where2)->find();
		if (!empty($arr)) {
			die("<script>alert('您未通过审核!');history.back(-1);</script>");
		}
		/*
		查看是否经过审核*/
		$where3['account'] = trim(I('post.account'));
		$where3['pass'] = secretPassword(I('post.pass'));
		$where3['status'] = array('eq', 1);
		$where3['pass_time'] = array('gt', 0);
		$where3['update_time'] = array('gt', 0);
		$arr = $obj->where($where3)->find();
		if (!empty($arr)) {
			session('mid', $arr['id']);
			session('account', $where3['account']);
			if (I('post.rememberMe') == true) {
				cookie('account', trim(I('post.account')));
				cookie('pass', trim(I('post.pass')));
			}
			M('Member')->where(array('id' => session('mid')))->setInc('logintimes', 1);
			$url = U('Index/index');
			header('Location:' . $url);
		} else {
			die("<script>alert('您未通过审核!');history.back(-1);</script>");
		}
	}

	public function loginOut() {
		session('mid', '');
		session('account', '');
		$url = U('Login/index');
		die("<script>alert('退出成功!');window.location.href='$url'</script>");
	}
	/**
	 * 忘记密码页面
	 * @return [type] [description]
	 */
	public function forgetPassword() {
		$this->display();
	}
	/**
	 * 设置新密码
	 */
	public function setNewPsd() {
		$id = I('get.p');
		$this->assign('id', $id);
		$this->display();
	}
	/**
	 * 设置新密码处理
	 */
	public function setNewPsdAction() {
		$post = trimPost(I('post.'));
		$id = $post['id'];
		if ($post['pass'] != $post['pass2']) {
			$url = U('Login/setNewPsd?p=' . $id);
			die("<script>alert('两次密码输入不一致！');window.location.href='$url'</script>");
		}
		$bool = M('Member')->where(array('id' => $id))->setField('pass', secretPassword($post['pass']));
		if ($bool !== false) {
			$url = U('Login/index');
			$this->success('修改成功！请重新登录！', $url);
			die("<script>alert('修改成功！请重新登录！');window.location.href='$url'</script>");
		} else {
			$url = U('Login/setNewPsd?p=' . $id);
			die("<script>alert('修改失败！请检查网络！');window.location.href='$url'</script>");
		}
	}
	/**
	 * 接口请求，发送验证码
	 * @return [type] [description]
	 */
	public function sendSmsCode() {
		$phone = trim(I('post.phone'));
		$return = D('Member')->is_phone_exist($phone);
		if ($return['code'] == 1) {
			$return['code'] = '-1';
			$return['msg'] = '手机号不存在！';
			$this->ajaxReturn($return);
		}
		$bool = D('Member')->getSmsCode($phone);
		$data['code'] = $bool ? 1 : -1;
		$this->ajaxReturn($data);
	}
	/**
	 * 重置密码处理
	 * @return [type] [description]
	 */
	public function resetPassword() {
		$phone = trim(I('post.phone'));
		$code = trim(I('post.code'));
		$sms_code = session('sms_code' . $phone);
		if ($code == $sms_code) {
			$id = M('Member')->where(array('m_phone' => $phone))->getField('id');
			$url = U('Login/setNewPsd?p=' . $id);
			die("<script>alert('进入重置密码页面！');window.location.href='$url'</script>");
		}

	}
	/**
	 * pc端修改密码逻辑
	 * @return [type] [description]
	 */
	public function pcResetPassword() {
		$post = trimPost(I('post.'));
		if ($post['pass'] != $post['pass2']) {
			die("<script>alert('两次密码输入不一致！');history.back(-1);</script>");
		}
		$sms_code = session('sms_code' . $post['phone']);
		if ($post['code'] != $sms_code) {
			die("<script>alert('验证码错误！');history.back(-1);</script>");
		}
		$res = M('Member')->where(array('m_phone' => $post['phone']))->setField('pass', secretPassword($post['pass']));
		if ($res !== false) {
			$url = U('Login/index');
			$this->success('修改成功！请重新登录！', $url);
			die("<script>alert('修改成功！请重新登录！');window.location.href='$url'</script>");
		} else {
			die("<script>alert('修改失败！请检查网络！');history.back(-1);</script>");
		}
	}
}