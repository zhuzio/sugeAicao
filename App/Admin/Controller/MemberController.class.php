<?php
namespace Admin\Controller;
use Admin\Controller\PublicController;
use Admin\Model\MemberModel;

header("Content-type: text/html; charset=utf-8");
class MemberController extends PublicController {
	protected $memberModel;
	protected $memberObj;
	public function __construct() {
		parent::__construct();
		$this->memberModel = new MemberModel();
		$this->memberObj = $this->memberModel->getmemberObj();

	}

	/*
    添加联合创始人页面*/
	public function member1Add() {

		$this->display();
	}
	/*
    添加总代理页面*/
	public function member2Add() {
		$this->display();
	}
	/**
	 * 添加联合创始人
	 * @return [type] [description]
	 */
	public function member1AddAction() {
		if (IS_POST) {
			$post = I('post.');
			$return = $this->memberModel->is_account_exist($post['account']);
			/*
				检查用户名信息
			*/
			if ($return['code'] == '-1') {
				$this->error($return['msg']);
			}
			$return1 = $this->memberModel->is_phone_exist($post['phone']);
			/*
				检查手机号
			*/
			if ($return1['code'] == '-1') {
				$this->error($return1['msg']);
			}
			$data['account'] = $post['account'];
			$data['name'] = $post['name'];
			if (I('post.recommend')) {
				$arr = $this->memberObj->where(array('account' => $post['recommend']))->find();
				$data['recommend_id'] = $arr['id'];
				$data['recommend_account'] = $arr['account'];
				if ($arr['is_lc'] == 1) {
					$data['recommend_lc'] = $arr['account'];
				} else {
					$data['recommend_lc'] = $arr['recommend_lc'];
				}
			} else {
				$data['recommend_id'] = 0;
				$data['recommend_account'] = 0;
				$data['recommend_lc'] = 0;
			}

			$data['m_phone'] = $post['phone'];
			$data['id_number'] = $post['id_number'];
			$data['is_lc'] = 1;
			$data['is_zd'] = 1;
			$data['status'] = 1;
			$data['reg_check'] = 1;
			$data['pass_time'] = time();
			$data['update_time'] = time();
			$data['reg_time'] = time();
			$data['pass'] = secretPassword($post['pass']);
			$n_id = $this->memberObj->add($data);
			$data1['s_id'] = $n_id;
			$data1['s_account'] = $post['account'];
			$data1['s_phone'] = $post['phone'];
			$data1['s_consignee'] = $post['name'];
			$data1['s_addr'] = $post['address'];
			$data1['status'] = 1;
			M('Address')->add($data1);
			if ($n_id > 0) {
				$this->success('添加成功！');
			} else {
				$this->error('添加失败！请检查网络');
			}
		}
	}
	/**
	 * 添加总代
	 * @return [type] [description]
	 */
	public function member2AddAction() {
		if (!IS_POST) {
			return false;
		}
		$post = I('post.');
		$data['account'] = $post['account'];
		$return = $this->memberModel->is_account_exist($post['account']);
		/*
				检查用户名信息
			*/
		if ($return['code'] == '-1') {
			$this->error($return['msg']);
		}

		$data['name'] = $post['name'];
		if (I('post.recommend')) {
			$arr = $this->memberObj->where(array('account' => $post['recommend']))->find();
			$data['recommend_id'] = $arr['id'];
			$data['recommend_account'] = $arr['account'];
			if ($arr['is_lc'] == 1) {
				$data['recommend_lc'] = $arr['account'];
			} else {
				$data['recommend_lc'] = $arr['recommend_lc'];
			}
		} else {
			$data['recommend_id'] = 0;
			$data['recommend_account'] = 0;
			$data['recommend_lc'] = 0;
		}

		$data['m_phone'] = $post['phone'];
		$data['id_number'] = $post['id_number'];
		$data['is_zd'] = 1;
		$data['status'] = 1;
		$data['reg_check'] = 1;
		$data['pass_time'] = time();
		$data['update_time'] = time();
		$data['reg_time'] = time();
		$data['pass'] = secretPassword($post['pass']);
		$n_id = $this->memberObj->add($data);
		$data1['s_id'] = $n_id;
		$data1['s_account'] = $post['account'];
		$data1['s_phone'] = $post['phone'];
		$data1['s_consignee'] = $post['name'];
		$data1['s_addr'] = $post['address'];
		$data1['status'] = 1;
		M('Address')->add($data1);
		if ($n_id > 0) {
			$this->success('添加成功！');
		} else {
			$this->error('添加失败！请检查网络');
		}
	}
	/*
    联创列表*/
	public function member1List() {
		$map['is_lc'] = array('eq', 1);
		$map['delete_time'] = array('eq', 0);
		$count = $this->memberObj->where($map)->count();
		$list = $this->memberModel->getLcList();
		$assign = array(
			'count' => $count,
			'list' => $list,
		);
		$this->assign($assign);
		$this->display();
	}
	/*
    总代列表*/
	public function member2List() {
		$map['is_zd'] = array('eq', 1);
		$map['is_lc'] = array('eq', 0);
		$map['delete_time'] = array('eq', 0);
		$count = $this->memberObj->where($map)->count();
		$list = $this->memberModel->getZdList();
		$assign = array(
			'count' => $count,
			'list' => $list,
		);
		$this->assign($assign);
		$this->display();
	}
	/**
	 * 会员编辑页面
	 * @return [type] [description]
	 */
	public function memberEdit() {
		$arr = $this->memberObj->where(array('id' => I('get.id')))->find();
		$this->assign('arr', $arr);
		$this->display();
	}
	/**
	 * 会员编辑处理
	 * @return [type] [description]
	 */
	public function memberEditAction() {
		if (IS_POST) {
			$post = I('post.');
			$find = $this->memberObj->where(array('account' => $post['account']))->find();
			if ($find['m_phone'] != $post['phone']) {
				$return1 = $this->memberModel->is_phone_exist($post['phone']);
				//检查手机号
				if ($return1['code'] == '-1') {
					$this->error($return1['msg']);
				}
			}
			$data['account'] = $post['account'];
			$data['name'] = $post['name'];
			if ($post['pass'] != '') {
				$data['pass'] = secretPassword($post['pass']);
			}
			$data['m_phone'] = $post['phone'];
			$data['id_number'] = $post['id_number'];
			$bool = $this->memberObj->where(array('account' => $post['account']))->save($data);
			if ($bool !== false) {
				$this->success('编辑成功！');
			} else {
				$this->error('编辑失败！请检查网络');
			}
		}
	}
	/*
    删除代理*/
	public function memberDel() {
		$id = I('post.id');
		$x = $this->memberObj->where(array('id' => $id))->setField('delete_time', time());
		$data['code'] = $x ? 1 : -1;
		$this->ajaxreturn($data);
	}
	/**
	 * 个人信息
	 * @return [type] [description]
	 */
	public function memberShow() {
		$memberInfo = $this->memberObj->where(array('account' => I('get.account')))->find();
		$memberInfo['recommend_name'] = M('Member')->where(array('id' => $memberInfo['recommend_id']))->getField('name');
		$memberInfo['lc_name'] = M('Member')->where(array('account' => $memberInfo['recommend_lc']))->getField('name');
		$this->assign('arr', $memberInfo);
		$this->display();
	}
	/*
    总代提货申请列表*/
	public function purchaseApplication() {
		$map['ai_thsq.update_time'] = array('eq', 0);
		$count = M('Thsq')->where($map)->count();
		$map['ai_member.delete_time'] = array('eq', 0);
		$thsq_list = M('Thsq')
			->field('ai_thsq.id,t_account,is_zd,t_amount,recommend_lc,p_name,lc_check,t_recommend,t_phone,name,s_addr,t_time,ai_thsq.update_time,ai_thsq.pass_time')
			->where($map)
			->join('ai_address on ai_thsq.addr_id = ai_address.id')
			->join('ai_member on ai_thsq.t_account = ai_member.account')
			->order('ai_thsq.id desc')
			->select();
		$is_mobile = isMobile() ? 1 : -1;
		$assign = array(
			'count' => $count,
			'thsq_list' => $thsq_list,
			'is_mobile' => $is_mobile,
		);
		$this->assign($assign);
		$this->display();
	}
	/*
    提货已处理列表*/
	public function purchaseList() {
		$map['ai_thsq.pass_time'] = array('gt', 0);
		$count = M('Thsq')->where($map)->count();
		$map['ai_member.delete_time'] = array('eq', 0);
		$thsq_list = M('Thsq')
			->field('ai_thsq.id,is_zd,t_account,t_amount,recommend_lc,p_name,is_fare_add,lc_check,t_recommend,t_phone,name,s_addr,t_time,ai_thsq.update_time,ai_thsq.pass_time')
			->where($map)
			->join('ai_address on ai_thsq.addr_id = ai_address.id')
			->join('ai_member on ai_thsq.t_account = ai_member.account')
			->order('ai_thsq.id desc')
			->select();
		$this->assign('thsq_list', $thsq_list);
		$assign = array(
			'count' => $count,
			'thsq_list' => $thsq_list,
		);
		$this->assign($assign);
		$this->display();
	}
	/*
    提货记录删除*/
	public function purchaseDel() {
		$map['id'] = I('post.id');
		$bool = M('Thsq')->where($map)->delete();
		$return['code'] = $bool ? 1 : -1;
		$this->ajaxReturn($return);
	}
	/**
	 * 通过申请
	 * @return [type] [description]
	 */
	public function purchaseAgree() {
		$settings = include APP_PATH . 'Common/Conf/settings.php';
		$map['ai_thsq.id'] = I('post.id');
		/*
		获取提货人及提货信息*/
		$info = M('Thsq')
			->field('ai_thsq.update_time as thsq_update_time,p_name,recommend_lc,is_zd,t_account,name,t_recommend,t_amount')
			->where($map)
			->join('ai_member on ai_thsq.t_account = ai_member.account')
			->find();
		if ($info['thsq_update_time'] > 0) {
			$data_return['code'] = '-1';
			$data_return['msg'] = '您已处理过！';
			$this->ajaxReturn($data_return);
		}
		/*
		更改申请状态为已通过*/
		$data_save['update_time'] = time();
		$data_save['pass_time'] = time();
		$bool = M('Thsq')->where(array('id' => I('post.id')))->save($data_save);
		if ($bool) {
			if ($info['is_zd'] == 0) {
				$zd_save['is_zd'] = 1;
				$zd_save['status'] = 1;
				$zd_save['pass_time'] = time();
				$zd_save['update_time'] = time();
				M('Member')->where(array('account' => $info['t_account']))->save($zd_save);
			}

			/*
				*如果更改成功给上级发佣金
				*首先获取提货多少盒 num
			*/
			if ($info['p_name'] == '真艾宝') {
				$num = $info['t_amount'];
			} else {
				$num = $info['t_amount'] / 5;
			}
			$arr = $this->memberModel->getRecommendInfo($info['t_recommend']);
			$boollc = false;
			/*
			如果数组不为空且身份是联创*/
			if (!empty($arr['recommend1_info']) && $arr['recommend1_info']['is_lc'] == 1) {
				$boollc = true;
			}
			/*
			如果提货人的上一级是联创*/

			if ($boollc) {
				$data['account'] = $arr['recommend1_info']['account'];
				$data['total'] = $arr['recommend1_info']['balance'];
				$data['contribute_account'] = $info['t_account'];
				$data['contribute_name'] = $info['name'];
				$data['plus'] = '+' . $num * ($settings['zd1'] + $settings['zd2']);
				$data['balance'] = $arr['recommend1_info']['balance'] + $num * ($settings['zd1'] + $settings['zd2']);
				$data['desc'] = '佣金+' . $num * ($settings['zd1'] + $settings['zd2']);
				$data['create_time'] = time();
				$insert = M('Fhjl')->add($data);
				if ($insert > 0) {
					$data_b['balance'] = $num * ($settings['zd1'] + $settings['zd2']) + $arr['recommend1_info']['balance'];
					$this->memberObj->where(array('account' => $arr['recommend1_info']['account']))->save($data_b);
				}
			}
			/*
			如果提货人上一级是总代且上一级的上一级存在*/

			if (
				!empty($arr['recommend1_info']) &&
				!empty($arr['recommend2_info']) &&
				$arr['recommend1_info']['is_lc'] == 0) {
				$data['account'] = $arr['recommend1_info']['account'];
				$data['total'] = $arr['recommend1_info']['balance'];
				$data['contribute_account'] = $info['t_account'];
				$data['contribute_name'] = $info['name'];
				$data['plus'] = '+' . $num * $settings['zd1'];
				$data['balance'] = $arr['recommend1_info']['balance'] + $num * $settings['zd1'];
				$data['desc'] = '佣金+' . $num * $settings['zd1'];
				$data['create_time'] = time();
				$insert = M('Fhjl')->add($data);
				if ($insert > 0) {
					$this->memberObj->where(array('account' => $arr['recommend1_info']['account']))->setInc('balance', $num * $settings['zd1']);
				}

				$data1['account'] = $arr['recommend2_info']['account'];
				$data1['total'] = $arr['recommend2_info']['balance'];
				$data1['contribute_account'] = $info['t_account'];
				$data1['contribute_name'] = $info['name'];
				$data1['plus'] = '+' . $num * $settings['zd2'];
				$data1['balance'] = $arr['recommend2_info']['balance'] + $num * $settings['zd2'];
				$data1['desc'] = '佣金+' . $num * $settings['zd2'];
				$data1['create_time'] = time();
				$insert = M('Fhjl')->add($data1);
				if ($insert > 0) {
					$this->memberObj->where(array('account' => $arr['recommend2_info']['account']))->setInc('balance', $num * $settings['zd2']);
				}
			}

			$arr1 = $this->memberModel->getLcInfo($info['recommend_lc']);
			$m1_balance = $this->memberObj->where(array('account' => $arr1['lc1_info']['account']))->getField('balance');
			$m2_balance = $this->memberObj->where(array('account' => $arr1['lc2_info']['account']))->getField('balance');
			/*
			给上一级联创发放佣金*/
			if ($arr1['lc1_info']['account'] != '') {
				$map3['account'] = $arr1['lc1_info']['account'];
				$data['account'] = $arr1['lc1_info']['account'];
				$data['contribute_account'] = $info['t_account'];
				$data['contribute_name'] = $info['name'];
				$data['total'] = $m1_balance;
				$data['plus'] = '+' . $num * $settings['lc'];
				$data['balance'] = $m1_balance + $num * $settings['lc'];
				$data['desc'] = '佣金+' . $num * $settings['lc'];
				$data['create_time'] = time();
				$add1 = M('Fhjl')->add($data);
				if ($add1 > 0) {
					$this->memberObj->where($map3)->setInc('balance', $num * $settings['lc']);
				}
			}
			/*
			给上二级联创发放佣金*/
			if ($arr1['lc2_info']['account'] != '') {
				$map4['account'] = $arr1['lc2_info']['account'];
				$data1['account'] = $arr1['lc2_info']['account'];
				$data1['contribute_account'] = $info['t_account'];
				$data1['contribute_name'] = $info['name'];
				$data1['total'] = $m2_balance;
				$data1['plus'] = '+' . $num * $settings['lc'];
				$data1['balance'] = $m2_balance + $num * $settings['lc'];
				$data1['desc'] = '佣金+' . $num * $settings['lc'];
				$data1['create_time'] = time();
				$add2 = M('Fhjl')->add($data1);
				if ($add2 > 0) {
					$this->memberObj->where($map4)->setInc('balance', $num * $settings['lc']);
				}

			}
			//检查总代升级，从下往上升，升级成功则截断退出，不再往上升级。
			$account = M('Thsq')->where(array('id' => I('post.id')))->getField('t_account');
			$mid = M('Member')->where(array('account' => $account))->getField('id');
			$this->memberModel->checkzdsj($mid);
			$data_return['code'] = 1;
			$data_return['msg'] = '处理成功！';
		} else {
			$data_return['code'] = 0;
			$data_return['msg'] = '处理失败请检查网络';
		}
		$this->ajaxReturn($data_return);
	}
	/**
	 * 拒绝申请
	 * @return [type] [description]
	 */
	public function purchaseRefuse() {
		$id = I('post.id');
		$data['update_time'] = time();
		$data['pass_time'] = 0;
		$data['refuse_time'] = time();
		$bool = M('Thsq')->where(array('id' => $id))->save($data);
		if ($bool !== false) {
			$return['code'] = '1';
			$return['msg'] = '处理成功！';
		} else {
			$return['code'] = '-2';
			$return['msg'] = '处理失败！请检查网络！';
		}
		$this->ajaxReturn($return);
	}
	/**
	 * 会员启用
	 * @return [type] [description]
	 */
	public function memberStart() {
		$mid = I('post.id');
		$bool = $this->memberObj->where(array('id' => $mid))->setField('status', 1);
		$data['code'] = $bool ? 1 : -1;
		$this->ajaxReturn($data);
	}
	/**
	 * 会员禁用
	 * @return [type] [description]
	 */
	public function memberStop() {
		$mid = I('post.id');
		$bool = $this->memberObj->where(array('id' => $mid))->setField('status', 0);
		$data['code'] = $bool ? 1 : -1;
		$this->ajaxReturn($data);
	}
	/**
	 * 检查推荐人
	 * @return [type] [description]
	 */
	public function check_recommend() {
		$return = $this->memberModel->check_account(I('post.recommend'));

		$this->ajaxReturn($return);
	}
	/**
	 * 未处理的提现列表
	 * @return [type] [description]
	 */
	public function withdraw() {
		$bankList = require_once ROOT_PATH . 'bankList.php';
		$map['ai_txjl.update_time'] = array('eq', 0);
		$list = M('Txjl')
			->field('ai_txjl.id,ai_txjl.t_account,name,t_yhk,t_time,ai_txjl.pass_time,t_money')
			->where($map)
			->join('ai_member on ai_txjl.t_id = ai_member.id')
			->select();
		$arr = [];
		foreach ($list as $val) {
			$val['ssyh'] = bankInfo($val['t_yhk'], $bankList);
			$arr[] = $val;
		}
		$this->assign('tx_list', $arr);
		$this->display();
	}
	/**
	 * 已处理的提现列表
	 * @return [type] [description]
	 */
	public function withdrawList() {
		$bankList = require_once ROOT_PATH . 'bankList.php';
		$map['ai_txjl.update_time'] = array('gt', 0);
		$list = M('Txjl')
			->field('ai_txjl.id,ai_txjl.t_account,name,t_yhk,t_time,ai_txjl.pass_time,t_money')
			->where($map)
			->join('ai_member on ai_txjl.t_id = ai_member.id')
			->select();
		$arr = [];
		foreach ($list as $val) {
			$val['ssyh'] = bankInfo($val['t_yhk'], $bankList);
			$arr[] = $val;
		}
		$this->assign('tx_list', $arr);
		$this->display();
	}
	/**
	 *提现申请拒绝
	 * @return [type] [description]
	 */
	public function withdrawRefuse() {
		$map['id'] = I('post.id');
		$data['refuse_time'] = time();
		$data['update_time'] = time();
		$res = M('Txjl')->where($map)->save($data);
		$arr = M('Txjl')->where($map)->find();
		if ($res > 0) {
			$y_ye = M('Member')->where(array('account' => $arr['account']))->getField('balance');
			$res1 = M('Member')->where(array('account' => $arr['account']))->setDec('balance', $arr['t_money']);
			if ($res1) {
				$x_ye = M('Member')->where(array('account' => $arr['account']))->getField('balance');
				$data['account'] = $arr['account'];
				$data['total'] = $y_ye;
				$data['plus'] = '+' . $arr['t_money'];
				$data['balance'] = $x_ye;
				$data['desc'] = '提现拒绝+' . $arr['t_money'];
				$data['create_time'] = time();
				$data['type'] = 2;
				M('Fhjl')->add($data);
			}
		}
		$return['code'] = $res > 0 ? 1 : -1;
		$this->ajaxReturn($return);
	}
	/**
	 * 通过提现申请
	 * @return [type] [description]
	 */
	public function withdrawAgree() {
		$map['id'] = I('post.id');
		$data['pass_time'] = time();
		$data['update_time'] = time();
		$res = M('Txjl')->where($map)->save($data);
		$return['code'] = $res > 0 ? 1 : -1;
		$this->ajaxReturn($return);
	}
	/**
	 * 提现记录删除
	 * @return [type] [description]
	 */
	public function withdrawDel() {
		$map['id'] = I('post.id');
		$res = M('Txjl')->where($map)->delete();
		$return['code'] = $res > 0 ? 1 : -1;
		$this->ajaxReturn($return);
	}

}