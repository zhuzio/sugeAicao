<?php
namespace Home\Controller;
use Common\Controller\CommonController;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class PublicController extends CommonController {
	protected $yeji;
	protected $settings;
	protected $memberInfo;
	public function __construct() {
		parent::__construct();
		$c = CONTROLLER_NAME;
		$mid = session('mid');
		if ($c != 'Login' && $c != 'Register') {
			//判断session
			if (!isset($mid)) {
				$this->redirect(U('Login/index'));
			}
			/*
			判断是否已禁用*/
			$status = M('Member')->where(array('id' => $mid))->getField('status');
			if ($status == 0) {
				die("<script>alert('您已被禁用!');</script>");
			}
			$this->memberInfo = M('Member')->where(array('id' => session('mid')))->find();
			$this->settings = include APP_PATH . 'Common/Conf/settings.php';
			$memberInfo = $this->memberInfo;
			$f_link = $this->server . U('Index/index');
			$address_link = $this->server . U('Info/my_center');
			$recommend_info = M('Member')->field('name,account,m_phone')->where(array('id' => $memberInfo['recommend_id']))->find();
			$lc_info = M('Member')->field('name,account,m_phone')->where(array('account' => $memberInfo['recommend_lc']))->find();
			$nums = D('Member')->xiajirenshu(session('mid'), 1);
			$rec_num['recommend_id'] = session('mid');
			$rec_num['pass_time'] = array('gt', 0);
			$num = M('Member')->where($rec_num)->count();
			//获取本人业绩
			$t_where['t_account'] = session('account');
			$t_where['pass_time'] = array('gt', 0);
			$m_amount = M('Thsq')->where($t_where)->sum('t_amount');
			//获取团队业绩
			$this->yeji = D('Member')->getShipments(session('mid')) + $m_amount;
			if ($memberInfo['is_lc'] != 1) {
				if (($num >= C('tjrs')) && ($this->yeji >= C('cwlcjhhs'))) {
					M('Member')->where(array('id' => session('mid')))->setField('is_lc', 1);
					D('Member')->upGrade(session('mid'));
				}
			}
			//证书编号
			$certificate = M('Certificate')->where(array('u_id' => session('mid')))->find();
			if (empty($certificate)) {
				$num = 6 - strlen(session('mid'));
				$str = '';
				for ($i = 0; $i < $num; $i++) {
					$str .= '0';
				}
				$bh = 'SG' . date('y') . date('m') . date('d') . $str . session('mid');
				$data['certificate_no'] = $bh;
				$data['u_id'] = session('mid');
				M('Certificate')->add($data);
				$this->certificate_no = $bh;
			} else {
				$this->certificate_no = $certificate['certificate_no'];
			}
			$assign = array(
				'f_link' => $f_link,
				'address_link' => $address_link,
				'tdyj' => $this->yeji,
				'recommend_info' => $recommend_info,
				'lc_info' => $lc_info,
				'memberInfo' => $memberInfo,
				'tdrs' => $nums,
			);
			$this->assign($assign);
			unset($assign);
		}
		C('DEFAULT_THEME', 'pc');
		if (isMobile()) {
			C('DEFAULT_THEME', 'wap');
			C('TMPL_EXCEPTION_FILE', PUBLIC_PATH . '404/error.html');
		}

	}
}