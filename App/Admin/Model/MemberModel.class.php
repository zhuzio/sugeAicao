<?php
namespace Admin\Model;
use Common\Model\BaseModel;

class MemberModel extends BaseModel {
	public function __construct() {
		parent::__construct();
	}
	/**
	 *获取提货人的上两级
	 */
	public function getRecommendInfo($tjr) {
		/*提货人上一级*/
		$recommend1_info = $this->memberObj
			->field('is_lc,is_zd,balance,account,recommend_account')
			->where(array('account' => $tjr))
			->find();

		/*提货人上一级的上一级*/
		$recommend2_info = $this->memberObj
			->field('is_lc,is_zd,balance,account,recommend_account')
			->where(array('account' => $recommend1_info['recommend_account']))
			->find();

		return array(
			'recommend1_info' => $recommend1_info,
			'recommend2_info' => $recommend2_info,
		);
	}
	public function getLcInfo($lc) {
		/*推荐人信息*/
		$info = $this->memberObj
			->field('recommend_lc,account')
			->where(array('account' => $lc))
			->find();
		$lc1_info = $this->memberObj
			->field('recommend_lc,account')
			->where(array('account' => $info['recommend_lc']))
			->find();
		$lc2_info = $this->memberObj
			->field('recommend_lc,account')
			->where(array('account' => $lc1_info['recommend_lc']))
			->find();
		return array(
			'lc1_info' => $lc1_info,
			'lc2_info' => $lc2_info,
		);
	}
	/*
	获取联创列表*/
	public function getLcList() {
		$where['is_lc'] = 1;
		$where['delete_time'] = array('eq', 0);
		return $this->memberObj->where($where)->select();
	}
	/*
	获取总代列表*/
	public function getZdList() {
		$where['is_lc'] = 0;
		$where['is_zd'] = 1;
		$where['delete_time'] = array('eq', 0);
		return $this->memberObj->where($where)->select();
	}
	/**
	 * 检查总代升级
	 * @param  [type] $rid [description]
	 * @return [type]      [description]
	 */
	public function checkzdsj($rid) {
		$find = $this->memberObj->find($rid);
		$num = $this->memberObj->where(array('recommend_id' => $rid))->count();
		if ($find['is_lc'] == 0) {
			$t_where['t_account'] = $find['account'];
			$t_where['pass_time'] = array('gt', 0);
			$m_amount = M('Thsq')->where($t_where)->sum('t_amount');
			$yeji = $this->getShipments($find['id']) + $m_amount;
			if ($yeji >= C('cwlcjhhs') && $num >= C('tjrs')) {
				M('Member')->where(array('id' => $find['id']))->setField('is_lc', 1);
				$this->upGrade_h($find['id'], $find['account']);
				exit;
			} else {
				$this->checkzdsj($find['recommend_id']);

			}
		}
	}

}
