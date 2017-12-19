<?php
namespace Home\Controller;
use Home\Controller\PublicController;

header("Content-type: text/html; charset=utf-8");
class InfoController extends PublicController {
	public function __construct() {
		parent::__construct();
	}
	public function index() {
		$this->display();
	}
	public function notice() {
		$list = M('Notice')->order('add_time desc')->select();
		$this->assign('notice_list', $list);
		$this->display();
	}
	/**
	 * 修改密码页面
	 * @return [type] [description]
	 */
	public function changePassword() {
		$this->display();
	}
	/**
	 * 修改密码处理逻辑
	 * @return [type] [description]
	 */
	public function changePasswordAction() {
		if (!IS_POST) {
			return;
		}
		$model = M('Member');
		$post = trimPost(I('post.'));
		$mid = session('mid');
		$forward_pwd = $model->where(array('id' => $mid))->getField('pass');
		if ($forward_pwd != secretPassword($post['y_psd'])) {
			$return['code'] = '-1';
			$return['msg'] = '原密码输入有误！';
			$this->ajaxReturn($return);
		}
		if ($post['psd'] != $post['c_psd']) {
			$return['code'] = '-2';
			$return['msg'] = '两次密码输入不一致！';
			$this->ajaxReturn($return);
		}
		$bool = $model->where(array('id' => $mid))->setField('pass', secretPassword($post['psd']));
		if ($bool !== false) {
			$return['code'] = '1';
		} else {
			$return['code'] = '-3';
			$return['msg'] = '修改失败请检查网络！';
		}
		$this->ajaxReturn($return);
	}
	/*
		添加地址页面
	*/
	public function addAddress() {
		$this->display();

	}
	//添加地址
	public function addAddressData() {
		$mid = $_SESSION['mid'];
		if (!empty($mid)) {
			$_POST['s_account'] = M('Member')
				->where(array('id' => $mid))
				->getField('account');
			$_POST['s_id'] = $mid;
			if ($_POST['status'] == '1') {
				M('Address')
					->where(array('s_id' => $mid, 'status' => '1'))
					->save(array('status' => 0));
			}
			$_POST['s_addr'] = $_POST['s_addr'] . $_POST['s_address'];
			unset($_POST['s_address']);
			$result = M('Address')->add($_POST);
			if ($result) {
				echo "<script> alert('添加成功');window.location.href='" . __MODULE__ . "/Info/addressManger'</script>";
			} else {
				echo "<script>alert('添加失败');window.history.go(-1)</script>";
			}
		} else {
			echo "<script>alert('您的数据不存在请从新登录');window.history.go(-1)</script>";
		}
	}
	/*
		        添加地址管理
	*/
	public function addressManger() {
		$mid = $_SESSION['mid'];
		$map['s_id'] = $mid;
		$map['delete_time'] = array('eq', 0);
		if (!empty($mid)) {
			$result = M('Address')->where($map)->order('status desc')->select();
			$this->assign('address', $result);
		}
		$this->display();
	}
	//删除收货地址
	public function delAddress() {
		if (session('mid') == '') {
			return;
		}
		$id = I('get.id');
		$mid = session('mid');
		$model = M('Address');
		$add_s = $model->where(array('id' => $id))->find();
		if ($add_s['status'] == '1') {
			$new_d = $model->where(array('s_id' => $mid))->limit(1)->order('id desc')->select();
			if (!empty($new_d)) {
				$new_status = $model
					->where(array('id' => $new_d[0]['id']))
					->save(array('status' => 1));
			}
		}
		$del = $model
			->where(array('id' => $id))
			->setField('delete_time', time());
		$return['code'] = $del > 0 ? 1 : -1;
		$this->ajaxReturn($return);
	}
	//修改收货地址默认
	public function changeAddressStatus() {
		if (session('mid') == '') {
			return;
		}
		$id = I('get.id');
		$mid = session('mid');
		$model = M('Address');
		$add_s = $model
			->where(array('s_id' => $mid, 'status' => 1))
			->save(array('status' => 0));
		$res = $model
			->where(array('id' => $id))
			->save(array('status' => 1));
		$return['code'] = $res !== false ? 1 : -1;
		$this->ajaxReturn($return);
	}
	/**
	 * 申请提货页面
	 */
	public function tiHuo() {
		$is_lc = M('Member')->where(array('id' => session('mid')))->getField('is_lc');
		if ($is_lc == 1) {
			$this->error('您没有提货权限！');
		}
		/*$lc_info = M('Member')->field('name,account,m_phone')->where(array('account' => $lc))->find();
			$strlen = (strlen($lc_info['name']) - 3) / 3;
			$str = returnstr($strlen);
		*/
		$type_info = M('Type')->select();
		$address_info = M('Address')->where(array('s_id' => session('mid')))->select();
		$phone = M('Address')->where(array('s_id' => session('mid'), 'status' => 1))->getField('s_phone');
		$assign = array(
			'type_info' => $type_info,
			'address_info' => $address_info,
			's_phone' => $phone,
		);
		$this->assign($assign);
		$this->display();
	}
	/**
	 * 获取地址对应的手机号
	 * @return [type] [description]
	 */
	public function getAddrPhone() {
		if (!IS_POST) {
			return;
		}
		$addr_id = I('post.addr_id');
		$phone = M('Address')->where(array('id' => $addr_id))->getField('s_phone');
		if (!empty($phone)) {
			$return['code'] = '1';
			$return['phone'] = $phone;
		} else {
			$return['code'] = '-1';
			$return['phone'] = '获取失败请检查网络！';
		}
		$this->ajaxReturn($return);
	}
	/**
	 * 提货处理逻辑
	 * @return [type] [description]
	 */
	public function tiHuoAction() {
		if (!IS_POST) {
			return;
		}
		$post = trimPost(I('post.'));
		$memberInfo = $this->memberInfo;
		$type_id = $post['p_id'];
		$p_name = M('Type')->where(array('id' => $type_id))->getField('p_name');
		$data['p_name'] = $p_name;
		$data['t_account'] = $memberInfo['account'];
		$data['t_recommend'] = $memberInfo['recommend_account'];
		$data['t_amount'] = $post['p_num'];
		$data['t_phone'] = $post['p_tel'];
		$data['addr_id'] = $post['p_addr'];
		$data['t_time'] = time();
		$n_id = M('Thsq')->add($data);
		if ($n_id > 0) {
			$data_return['code'] = '1';
		} else {
			$data_return['code'] = '-1';
		}
		$this->ajaxReturn($data_return);

	}

	/**
	 * 修改个人资料
	 */
	public function sendCode() {
		$phone = trim(I('post.phone'));
		$return = D('Member')->is_phone_exist($phone);
		if ($return['code'] == '-1') {
			$return1['code'] = '-1';
			$data1['msg'] = '手机号已存在！';
			$this->ajaxReturn($return);
		}
		$bool = D('Member')->getSmsCode($phone);
		$data['code'] = $bool ? 1 : -1;
		$this->ajaxReturn($data);
	}
	//修改手机号
	public function changeInfo() {
		if (IS_POST) {
			$phone = I('post.phone');
			$code = I('post.code');
			$mid = $_SESSION['mid'];
			if ($code == $_SESSION['sms_code' . $phone]) {
				$res = M('Member')->where(array('id' => $mid))->save(array('m_phone' => $phone));
				if ($res > 0) {
					$return['code'] = '-1';
					$return['msg'] = '修改失败，请检查网络！';
				} else {
					$return['code'] = '1';
				}
			} else {
				$return['code'] = '-1';
				$return['msg'] = '修改失败，请检查网络！';
			}
			$this->ajaxReturn($return);
		} else {
			$this->display();
		}
	}
	/**
	 * 分红记录
	 */
	public function dividendRecord() {
		$count = M('Fhjl')->where(array('account' => session('account')))->count();
		$page = createPage($count, C('page_row'));
		$list = M('Fhjl')->where(array('account' => session('account')))->limit($page->firstRow, $page->listRows)->order('id desc')->select();
		$assign = array(
			'dividend_list' => $list,
			'page' => $page->show(),
		);
		$this->assign($assign);
		$this->display();
	}

	/**
	 * 提现记录
	 */
	public function withdrawRecord() {
		$count = M('Txjl')->where(array('t_account' => session('account')))->count();
		$page = createPage($count, C('page_row'));
		$list = M('Txjl')->where(array('t_account' => session('account')))->limit($page->firstRow, $page->listRows)->order('t_time desc')->select();
		$assign = array(
			'tx_list' => $list,
			'page' => $page->show(),
		);
		$this->assign($assign);
		$this->display();
	}
	/**
	 * 已提货列表
	 * @return [type] [description]
	 */
	public function tiHuoRecord() {

		$count = M('Thsq')
			->join('ai_address on ai_thsq.addr_id = ai_address.id')
			->where(array('t_account' => session('account')))
			->count();
		$page = createPage($count, 10);
		$list = M('Thsq')
			->join('ai_address on ai_thsq.addr_id = ai_address.id')
			->where(array('t_account' => session('account')))
			->limit($page->firstRow, $page->listRows)
			->order('t_time desc')
			->select();
		$assign = array(
			'th_list' => $list,
			'page' => $page->show(),
		);
		$this->assign($assign);
		$this->display();
	}
	/**
	 * 申请提现页面
	 */
	public function withdraw() {
		$settings = $this->settings;
		$where['type'] = array('eq', 1);
		$where['account'] = session('account');
		$date = strtotime(date('Y-m-01', time()));
		$where['create_time'] = array('egt', $date);
		$plus = M('Fhjl')->where($where)->sum('plus');
		$plus = $plus != '' ? $plus : 0;
		$ktje = $this->memberInfo['balance'] - $plus;
		$assign = array(
			'settings' => $settings,
			'plus' => $ktje,
			'memberInfo' => $this->memberInfo,
		);
		$this->assign($assign);
		$this->display();
	}
	/** 提现处理
	 * @return [type] [description]
	 */
	public function withdrawAction() {
		$settings = $this->settings;
		$memberInfo = $this->memberInfo;
		$post = trimPost(I('post.'));
		/*
			有提现金额不是规定额度内的
		*/
		$t_money = $post['money'];
		if ($t_money < $settings['zdtxed']) {
			$return['code'] = '-2';
			$return['msg'] = '提现额度为！' . $settings['zdtxed'] . '起';
			$this->ajaxReturn($return);
		}
		/*
		 * 有提现金额大于可提金额的*/
		$where['type'] = array('eq', 1);
		$where['account'] = session('account');
		$date = strtotime(date('Y-m-01', time()));
		$where['create_time'] = array('egt', $date);
		$plus = M('Fhjl')->where($where)->sum('plus');
		$plus = $plus != '' ?: 0;
		$ktje = $this->memberInfo['balance'] - $plus;
		if ($post['money'] > $ktje) {
			$return['code'] = '-5';
			$return['msg'] = '提现金额大于可提现金额！';
			$this->ajaxReturn($return);
		}
		/*
			有提现金额大于账户余额的
		*/
		if ($post['money'] > $memberInfo['balance']) {
			$return['code'] = '-4';
			$return['msg'] = '账户余额不足！';
			$this->ajaxReturn($return);
		}
		/*
			有未到指定日期提现的
		*/
		$day = date('d');
		if ($day < $settings['txrq']) {
			$return['code'] = '-1';
			$return['msg'] = '每月' . $settings['txrq'] . '号后可提现！';
			$this->ajaxReturn($return);
		}

		if ($t_money > $settings['zgtxed']) {
			$return['code'] = '-3';
			$return['msg'] = '提现额度最高为！' . $settings['zdtxed'];
			$this->ajaxReturn($return);
		}
		$data['t_id'] = session('mid');
		$data['t_account'] = session('account');
		$data['t_money'] = $t_money;
		$data['t_yhk'] = $post['yhk'];
		$data['t_time'] = time();

		$n_id = M('Txjl')->add($data);
		if ($n_id > 0) {
			$y_ye = M('Member')->where(array('id' => session('mid')))->getField('balance');
			M('Member')->where(array('id' => session('mid')))->setDec('balance', $t_money);
			$x_ye = M('Member')->where(array('id' => session('mid')))->getField('balance');
			$data['account'] = session('account');
			$data['total'] = $y_ye;
			$data['plus'] = '-' . $t_money;
			$data['balance'] = $x_ye;
			$data['desc'] = '提现-' . $t_money;
			$data['create_time'] = time();
			$data['type'] = 2;
			$insert = M('Fhjl')->add($data);
			$return['code'] = $insert > 0 ? 1 : -1;
			$this->ajaxReturn($return);
		}

	}
	/**
	 * 意见反馈页面
	 * @return [type] [description]
	 */
	public function feedBack() {
		$count = M('Opinion')->where(array('t_account' => session('account')))->count();
		$page = createPage($count, 10);
		$list = M('Opinion')
			->where(array('t_account' => session('account')))
			->limit($page->firstRow, $page->listRows)
			->order('create_time desc')->select();
		$assign = array(
			'yj_list' => $list,
			'page' => $page->show(),
		);
		$this->assign($assign);
		$this->display();
	}
	/**
	 * 意见反馈处理
	 * @return [type] [description]
	 */
	public function feedBackAction() {
		$opinion = I('post.opinion');
		$data['t_account'] = session('account');
		$data['opinion'] = $opinion;
		$data['create_time'] = time();
		$x = M('Opinion')->add($data);
		$return['code'] = $x > 0 ? 1 : -1;
		$this->ajaxReturn($return);
	}
	/**
	 * 个人中心
	 * @return [type] [description]
	 */
	public function my_center() {

		$this->display();

	}
}