<?php
namespace Admin\Model;
use Common\Model\BaseModel;

class AdminModel extends BaseModel {
	public function __construct() {
		parent::__construct();
	}

	public function getAdminInfo($uid) {
		$arr = $this->adminObj
			->where(array('id' => $uid))
			->join("ai_role on ai_admin.role_id=ai_role.role_id")
			->find();
		return $arr;
	}
	public function getAdminAll() {
		$count = $this->adminObj->count(); // 查询满足要求的总记录数
		$Page = new \Org\Hl\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show(); // 分页显示输出
		// 进行分页数据查询
		$where['delete_time'] = array('eq', 0);
		$list = $this->adminObj
			->where($where)
			->join("ai_role on ai_admin.role_id=ai_role.role_id")
			->order('addtime')
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$assign = array(
			'count' => $count,
			'adminList' => $list,
			'show' => $show,
		);
		return $assign;
	}
	public function roleAdd($role_name, $admin_arr) {
		foreach ($admin_arr as $v) {
			$autharr[] = M("Auth")->where(array('auth_id' => $v))->getField("auth_id,auth_name,auth_a");
		}
		$role_auth_ids = '';
		$desc = "";
		$auth_a = '';
		foreach ($autharr as $v) {
			if ($v != "") {
				foreach ($v as $value) {

					$role_auth_ids .= $value["auth_id"] . ',';
					$desc .= $value['auth_name'] . ",";
					$auth_a .= $value["auth_a"] . ",";
				}}}

		$data['role_name'] = $role_name;
		$data["role_auth_ac"] = "welcome,loginOut," . $auth_a;
		$data['role_auth_ids'] = $role_auth_ids;
		$data["desc"] = "你有" . $desc . "的权利";
		return M("Role")->add($data);
	}
	public function roleEdit($role_name, $role_id, $admin_arr) {
		foreach ($admin_arr as $v) {
			$autharr[] = M("Auth")->where(array('auth_id' => $v))->getField("auth_id,auth_name,auth_a");
		}
		$role_auth_ids = '';
		$desc = "";
		$auth_a = '';
		foreach ($autharr as $v) {
			if ($v != "") {
				foreach ($v as $value) {

					$role_auth_ids .= $value["auth_id"] . ',';
					$desc .= $value['auth_name'] . ",";
					$auth_a .= $value["auth_a"] . ",";
				}}}

		$data['role_name'] = $role_name;
		$data["role_auth_ac"] = "welcome,loginOut," . $auth_a;
		$data['role_auth_ids'] = $role_auth_ids;
		$data["desc"] = "你有" . $desc . "的权利";
		return M("Role")->where(array('role_id' => $role_id))->save($data);
	}
	public function getAuth1() {
		$id = session("adminId");
		$role_auth_ids = $this->adminObj
			->where(array('id' => $id))
			->join("ai_role on ai_admin.role_id=ai_role.role_id")
			->getField('role_auth_ids');
		$brr = explode(",", $role_auth_ids);

		foreach ($brr as $v) {
			if ($v != "") {
				$crr[] = M('Auth')->where(array('auth_id' => $v))->find();
			}
		}
		foreach ($crr as $v) {
			if ($v['auth_pid'] == 0) {
				$drr[] = $v;
			}
		}
		return $drr;
	}
	public function getAuth2() {
		$id = session("adminId");
		$role_auth_ids = $this->adminObj
			->where(array('id' => $id))
			->join("ai_role on ai_admin.role_id=ai_role.role_id")
			->getField('role_auth_ids');
		$brr = explode(",", $role_auth_ids);
		foreach ($brr as $v) {
			if ($v != "") {
				$crr[] = M('auth')->where(array('auth_id' => $v))->find();
			}
		}
		foreach ($crr as $v) {
			if ($v['auth_pid'] != 0) {
				$drr[] = $v;
			}
		}
		return $drr;
	}

}