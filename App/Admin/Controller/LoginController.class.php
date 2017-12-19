<?php
namespace Admin\Controller;
use Common\Controller\CommonController;

class LoginController extends CommonController {
	public function index() {

		$this->display();
	}
	/**
	 * 登录处理
	 * @return [type] [description]
	 */
	public function loginAct() {
		$obj = M('Admin');
		$where['state'] = array('eq', 0);
		$where['name'] = I('post.userName');
		$where['pass'] = secretPassword(I('post.password'));
		$where['delete_time'] = array('eq', 0);
		$arr = $obj->where($where)->find();
		if (!empty($arr)) {
			die("<script>alert('您已被禁用!');history.go(-1)</script>");
		}
		$where['state'] = array('eq', 1);
		$where['name'] = I('post.userName');
		$where['pass'] = secretPassword(I('post.password'));
		$where['delete_time'] = array('eq', 0);
		$arr = $obj->where($where)->find();
		if (!empty($arr)) {
			session('adminId', $arr['id']);
			session('adminName', $where['name']);
			if (I('post.rememberMe') == true) {
				cookie('name', $where['name']);
				cookie('pass', trim(I('post.password')));
			}
			M('Admin')->where(array('id' => session('adminId')))->setInc('logintimes', 1);
			$url = U('Index/index');
			$this->success('登入成功!', $url);
		} else {
			$this->error('用户名或密码不正确!');
		}
	}
	/**
	 * 退出登录处理
	 * @return [type] [description]
	 */
	public function loginOut() {
		unset($_SESSION['adminId']);
		unset($_SESSION['adminName']);
		$url = U('Login/index');
		$this->success('退出成功', $url);
	}
	/**
	 * 忘记密码
	 * @return [type] [description]
	 */
	public function findPassword() {
		$this->display();
	}
	public function findPasswordAction() {

	}
}