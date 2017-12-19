<?php
namespace Common\Model;
use Think\Model;

class BaseModel extends Model {
	protected $memberObj;
	protected $adminObj;
	public function __construct() {
		$this->memberObj = $this->getMemberObj();
		$this->adminObj = $this->getAdminObj();
	}
	public function getMemberObj() {
		return M('Member');
	}
	public function getAdminObj() {
		return M('Admin');
	}
	/*
	获取团队出货量*/
	public function getShipments($mid) {
		static $shipments = 0;
		$where['recommend_id'] = $mid;
		$where['is_lc'] = 0;
		$members = $this->memberObj
			->field('id,account,is_lc')
			->where($where)
			->select();
		foreach ($members as $val) {
			if (!empty($val) && $val['is_lc'] == 0) {
				$map['t_account'] = $val['account'];
				$map['pass_time'] = array('gt', 0);
				$amount = M('Thsq')->where($map)->sum('t_amount');
				$shipments += $amount;
				return $this->getShipments($val['id']);
			}
		}
		return $shipments;
	}
	/**
	 * 获取团队总人数
	 * @param  id
	 * @param  nums 人数
	 * @return num
	 */
	public function xiajirenshu($id, $nums) {
		$arr = $this->memberObj->where(['recommend_id' => $id])->select();

		$num = count($arr);
		$nums += $num;
		foreach ($arr as $value) {
			$id = $value['id'];

			if ($id && $value['is_lc'] == 0) {
				$this->xiajirenshu($id, $nums);
			}
		}
		return $nums;
	}
	/*
	成为联合创始人*/
	public function upGrade($mid) {
		$members = $this->memberObj
			->field('id,is_lc,account')
			->where(array('recommend_id' => $mid))
			->select();

		$data['recommend_lc'] = session('account');
		foreach ($members as $val) {
			if (!empty($val) && $val['is_lc'] == 0) {
				$this->memberObj->where(array('id' => $val['id']))->save($data);
				$this->upGrade($val['id']);
			}
		}
	}
	/*
	后台同意提货申请佣金发放完毕后，给符合条件的人升联合创始人*/
	public function upGrade_h($mid, $account) {
		$members = $this->memberObj
			->field('id,is_lc,account')
			->where(array('recommend_id' => $mid))
			->select();

		$data['recommend_lc'] = $account;
		foreach ($members as $val) {
			if (!empty($val) && $val['is_lc'] == 0) {
				$this->memberObj->where(array('id' => $val['id']))->save($data);
				$this->upGrade_h($val['id']);
			}
		}
	}
	/**
	 * 获取团队树
	 */
	public function getTree($id) {
		$base = $this->getTreeBaseInfo($id);
		$znote = $this->getTreeInfo($id);
		$znote[] = $base;
		return $znote;
	}
	/**
	 * @param  mid
	 * @return 个人信息
	 */
	public function getTreeBaseInfo($id) {
		if (!$id) {
			return;
		}
		$r = M("Member")->where(array(
			'id' => $id,
		))->find();
		$nums = 0;
		$nums = $this->xiajirenshu($id, $nums);
		$nums++;
		$map['t_account'] = $r['account'];
		$map['pass_time'] = array('gt', 0);
		$yeji = M('Thsq')->where($map)->sum('t_amount');
		if (!empty($r)) {
			return array(
				"id" => $r['id'],
				"pId" => $r['recommend_id'],
				"name" => $r['account'] . " 团队人数：" . $nums . " 业绩" . $yeji,
			);

		}

		return;
	}
	/**
	 * @param  mid
	 * @return
	 */
	public function getTreeInfo($id) {
		static $trees = array();
		$ids = self::get_childs($id);
		if (!$ids) {
			return 0;
		}
		foreach ($ids as $v) {
			if (!empty($v)) {
				$trees[] = $this->getTreeBaseInfo($v);
				$this->getTreeInfo($v);
			}
		}
		return $trees;
	}
	/**
	 * @param  mid
	 * @return 获取直推人信息
	 */
	static function get_childs($id) {
		if (!$id) {
			return null;
		}
		$childs_id = array();
		$childs = M("Member")->field("id,is_lc")->where(array(
			'recommend_id' => $id,
		))->select();
		if (empty($childs)) {
			return;
		}
		foreach ($childs as $v) {
			if ($v['is_lc'] == 0) {
				$childs_id[] = $v['id'];
			}
		}
		if ($childs_id) {
			return $childs_id;
		}
		return;
	}
	/**
	 * 注册时检查推荐人信息
	 * @return [type] [description]
	 */
	public function check_account($account) {
		$arr = $this->memberObj->where(array('account' => $account))->find();
		if (empty($arr)) {
			$data['code'] = '-3';
			$data['msg'] = '帐号不存在！';
			return $data;
		}
		$map['account'] = $account;
		$map['delete_time'] = array('gt', 0);
		$arr = $this->memberObj->where($map)->find();
		if (!empty($arr)) {
			$data['code'] = '-1';
			$data['msg'] = '帐号不存在！';
			return $data;
		}

		$map1['account'] = $account;
		$map1['status'] = array('eq', 1);
		$arr2 = $this->memberObj->where($map1)->find();
		if (empty($arr2)) {
			$data['code'] = '-2';
			$data['msg'] = '此人已被禁用！';
		} else {
			$data['code'] = '1';
			$data['msg'] = $arr2['name'];
		}
		return $data;
	}
	/**
	 * 注册是检查用户名是否存在
	 * @return boolean [description]
	 */
	public function is_account_exist($account) {
		$map['account'] = $account;
		$arr = $this->memberObj->where($map)->find();
		if (!empty($arr)) {
			$data['code'] = '-1';
			$data['msg'] = '帐号已存在！';
		} else {
			$data['code'] = '1';
		}
		return $data;
	}
	/**
	 * 注册是检查手机号是否存在
	 * @return boolean [description]
	 */
	public function is_phone_exist($phone) {
		$map['m_phone'] = $phone;
		$arr = $this->memberObj->where($map)->find();
		if (!empty($arr)) {
			$data['code'] = '-1';
			$data['msg'] = '手机号已存在！';
		} else {
			$data['code'] = '1';
		}
		return $data;
	}
}