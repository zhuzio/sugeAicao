<?php
namespace Admin\Controller;
use Admin\Controller\PublicController;
use Admin\Model\AdminModel;

class AdminController extends PublicController {
	protected $adminModel;
	protected $adminObj;
	public function __construct() {
		parent::__construct();
		$this->adminModel = new AdminModel();
		$this->adminObj = $this->adminModel->getAdminObj();

	}
	/*
	管理员列表*/
	public function adminList() {
		$assign = $this->adminModel->getAdminAll();
		$this->assign($assign);
		$this->display();
	}
	/*
	管理员搜索*/
	public function adminSearch() {
		$arr = $this->adminModel->adminSearch(
			I('post.time1'),
			I('post.time2'),
			I('post.name')
		);
		$this->assign($arr);
		$this->display('adminList');
	}
	/*
	添加管理员页面*/
	public function adminAdd() {
		$roleList = M('Role')->field('role_id,role_name')->select();
		$this->assign('roleList', $roleList);
		$this->display();
	}
	/*
	添加管理员处理*/
	public function adminAddAct() {
		$data = trimPost(I('post.'));
		$data['pass'] = secretPassword($data['pass']);
		$data['addtime'] = time();
		$x = $this->adminObj->add($data);
		$data['status'] = $x > 0 ? 1 : -1;
		$this->ajaxreturn($data);
	}

	/*
	删除管理员*/
	public function adminDel() {
		$id = I('post.id');
		$x = $this->adminObj->where(array('id' => $id))->setField('delete_time', time());
		$data['code'] = $x ? 1 : -1;
		$this->ajaxreturn($data);
	}
	/*
	管理员编辑页面*/
	public function adminEdit() {
		$roleList = M('Role')->field('role_id,role_name')->select();
		$info = $this->adminModel->getAdminInfo(I('get.id'));
		$assign = array(
			'roleList' => $roleList,
			'info' => $info,
		);
		$this->assign($assign);
		$this->display();
	}
	/*
	管理员编辑处理*/
	public function adminEditAction() {
		$post = trimPost(I('post.'));
		if ($post['pass'] != '') {
			$data['pass'] = secretPassword($post['pass']);
		}
		$data['email'] = $post['email'];
		$data['sex'] = $post['sex'];
		$data['phone'] = $post['phone'];
		$data['role_id'] = $post['role_id'];

		$x = $this->adminObj->where(array('id' => $post['id']))->save($data);
		$data['status'] = $x > 0 ? 1 : -1;
		$this->ajaxreturn($data);
	}
	/*
	管理员禁用*/
	public function adminPowerOff() {
		$data['state'] = 0;
		$map['id'] = I('post.adminId');
		$save = $this->adminObj->where($map)->save($data);
		if ($save !== false) {
			$data1['code'] = 1;
			$data1['msg'] = '已禁用！';
		} else {
			$data1['code'] = 0;
			$data1['msg'] = '禁用失败！请检查网络！';
		}
		$this->ajaxreturn($data1);
	}
	/*
	管理员启用*/
	public function adminPowerOn() {
		$data['state'] = 1;
		$map['id'] = I('post.adminId');
		$save = $this->adminObj->where($map)->save($data);
		if ($save !== false) {
			$data1['code'] = 1;
			$data1['msg'] = '已启用！';
		} else {
			$data1['code'] = 0;
			$data1['msg'] = '启用失败！请检查网络！';
		}
		$this->ajaxreturn($data1);
	}
	/*
	权限列表页面*/
	public function adminPermission() {
		$arr = getAuthTree(M("Auth"));
		$num = M("Auth")->count();
		$assign = array(
			'num' => $num,
			'arr' => $arr,
		);
		$this->assign($assign);
		$this->display();
	}
	/*
	新增权限页面*/
	public function adminPermissionAdd() {
		$arr = M("Auth")->where(array('auth_pid' => 0))->select();
		$this->assign("arr", $arr);
		$this->display();
	}
	/*新增权限处理*/
	public function adminPermissionAddAction() {
		$id = M("Auth")->add(I('post.'));
		$arr['auth_path'] = I('post.auth_pid') . "-" . $id;
		M('Auth')->where(array('auth_id' => $id))->save($arr);
	}
	/*
	权限的编辑页面*/
	public function adminPermissionEdit() {
		$info = M('Auth')->where(array('auth_id' => I('get.auth_id')))->find();
		$authList = M("Auth")->where(array('auth_pid' => 0))->select();
		$assign = array(
			'auth_pid' => $info['auth_pid'],
			'info' => $info,
			'authList' => $authList,
		);
		$this->assign($assign);
		$this->display();
	}
	/*
	权限编辑的处理页面*/
	public function adminPermissionEditAction() {
		$id = M("Auth")->where(array('auth_id' => I('post.auth_id')))->save(I('post.'));
		$arr['auth_path'] = I('post.auth_pid') . "-" . $id;
		M('Auth')->where(array('auth_id' => $id))->save($arr);
	}
	/*
	权限删除处理*/
	public function adminPermissionDel() {
		$auth_id = I('post.id');
		$arr = M('Auth')->where(array('auth_pid' => $auth_id))->find();
		if (!empty($arr)) {
			$data['code'] = 0;
			$data['msg'] = '该权限下还有子权限,请先删除子权限！';
		} else {
			$del = M('Auth')->where(array('auth_id' => $auth_id))->delete();
			if ($del !== false) {
				$data['code'] = 1;
				$data['msg'] = '删除成功！';
			} else {
				$data['code'] = 1;
				$data['msg'] = '删除失败！请检查网络！';
			}
		}
		$this->ajaxreturn($data);

	}
	/*
	角色列表页面*/
	public function adminRole() {
		$roleList = M('Role')->select();
		$this->assign('roleList', $roleList);
		$this->display();
	}
	/*
	添加角色页面*/
	public function adminRoleAdd() {
		$where1['auth_pid'] = array('eq', 0);
		$where2['auth_pid'] = array('neq', 0);
		$arr1 = M('Auth')->where($where1)->select();
		$arr2 = M('Auth')->where($where2)->select();
		$assign = array(
			'arr1' => $arr1,
			'arr2' => $arr2,
		);
		$this->assign($assign);
		$this->display();
	}
	/*
	添加角色处理*/
	public function adminRoleAddAction() {
		$role_name = I('post.roleName');
		$arr = I('post.user-Character-0-0-0');
		$this->adminModel->roleAdd($role_name, $arr);
	}
	/*
	编辑角色页面*/
	public function adminRoleEdit() {
		$arr = M('Role')->field('role_name,role_auth_ids')->where(array('role_id' => I('get.role_id')))->find();
		$role_arr = explode(",", $arr['role_auth_ids']);
		$where1['auth_pid'] = array('eq', 0);
		$where2['auth_pid'] = array('neq', 0);
		$arr1 = M('Auth')->where($where1)->select();
		$arr2 = M('Auth')->where($where2)->select();
		$assign = array(
			'role_id' => I('get.role_id'),
			'role_arr' => $role_arr,
			'role_name' => $arr['role_name'],
			'arr1' => $arr1,
			'arr2' => $arr2,
		);
		$this->assign($assign);
		$this->display();
	}
	/*
	角色编辑处理*/
	public function adminRoleEditAction() {
		$role_name = I('post.roleName');
		$role_id = I('post.role_id');
		$arr = I('post.user-Character-0-0-0');
		$this->adminModel->roleEdit($role_name, $role_id, $arr);
	}
	/*
	角色删除处理*/
	public function adminRoleDel() {
		$role_id = I('post.id');
		/*
		角色下还有会员不能删除*/
		$where['role_id'] = $role_id;
		$where['delete_time'] = array('eq', 0);
		$arr = M('Admin')->where($where)->find();
		if (!empty($arr)) {
			$data['code'] = 0;
			$data['msg'] = '此角色下还有会员！请先删除用户！';
		} else {
			$del = M('Role')->where(array('role_id' => $role_id))->delete();
			if ($del) {
				$data['code'] = 1;
				$data['msg'] = '删除成功！';
			} else {
				$data['code'] = 0;
				$data['msg'] = '删除失败！请检查网络！';
			}
		}
		$this->ajaxreturn($data);
	}

}