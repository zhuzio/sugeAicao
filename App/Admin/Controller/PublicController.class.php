<?php
namespace Admin\Controller;
use Common\Controller\CommonController;

/**
 * Class AuthController
 * @package Admin\Controller
 * 权限控制器
 */
class PublicController extends CommonController {

	public function __construct() {
		parent::__construct();
		//判断session
		$admin_id = session('adminId');
		if (!isset($admin_id)) {
			redirect(U("Login/index"));
		}
		/*
		判断是否已禁用*/
		$state = M('Admin')->where(array('id' => $admin_id))->getField('state');
		if ($state == 0) {
			die("<script>alert('您已被禁用!');</script>");
		}
		//如果登录
		//echo '当前控制器是'.CONTROLLER_NAME ."当前方法名是".ACTION_NAME;
		$c = CONTROLLER_NAME;
		$where['id'] = $admin_id;
		$role_auth_ids = M('Admin')
			->where($where)
			->join("ai_role on ai_admin.role_id=ai_role.role_id")
			->getField('role_auth_ids');
		$brr = explode(",", $role_auth_ids);
		foreach ($brr as $v) {
			if ($v != "") {
				$drr[] = M('Auth')->where(array('auth_id' => $v))->getField('auth_c');
			}
		}
		$allow = array('Index', 'Login');
		$arr = array_merge($drr, $allow);

		if (!in_array($c, $arr)) {
			//如果不在里面,就说明我们没有权限来查看
			//除了以上也能访问
			die("<script>alert('您无权限访问!');</script>");

		}
	}
}