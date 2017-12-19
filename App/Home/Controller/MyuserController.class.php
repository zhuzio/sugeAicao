<?php
namespace Home\Controller;
use Home\Controller\PublicController;

header("Content-type: text/html; charset=utf-8");
class MyuserController extends PublicController {
	/*
	我的团队*/
	public function teamList() {
		$this->display();
	}
	/*
    	推荐列表*/
	public function recommendList() {
		$list = M('Member')->where(array('recommend_id' => session('mid')))->count();
		$Page = createPage($list, C('page_row'));
		$list = M('Member')
			->where(array('recommend_id' => session('mid')))
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$this->assign('recommend_list', $list);
		$this->assign('page', $Page->show());
		$this->display();
	}

	public function getTree() {
		if (I('post.account')) {
			$id = M('Member')->where(array('account' => I('post.account')))->getField('id');
		} else {
			$id = session('mid');
		}
		$tree = D('Member')->getTree($id);
		if (!empty($tree) && count($tree) > 1) {
			$data['code'] = 1;
			$data['msg'] = '获取成功';
			$data['data'] = $tree;
		} else {
			$data['code'] = 0;
			$data['msg'] = '您还没有下级';
		}
		$this->ajaxreturn($data);
	}

	/*
	待审核列表*/
	public function checkList() {
		$arr = M('Member')->find(session('mid'));
		if ($arr['is_lc'] == 1) {
			$where['recommend_lc'] = session('account');
			$where['is_zd'] = 0;
			$where['ai_member.update_time'] = array('eq', 0);
			$where['ai_member.reg_check'] = array('eq', 0);
			$count = M('Member')->where($where)->count();
			$page = createPage($count, C('page_row'));
			$list = M('Member')
				->field('name,ai_member.id,ai_address.s_addr,account,recommend_account,m_phone,reg_time')
				->join('ai_address on ai_member.id = ai_address.s_id')
				->where(array('recommend_lc' => session('account'), 'ai_address.status' => 1, 'ai_member.reg_check' => 0, 'update_time' => 0))
				->limit($page->firstRow . ',' . $page->listRows)
				->select();
			$this->assign('check_list', $list);
			$this->assign('page', $page->show());
		}
		$this->display();
	}
	/**
	 * 总代注册同意
	 * @return [type] [description]
	 */
	public function registerAgree() {
		$id = I('post.id');
		$check = M('Member')->where(array('id' => $id))->getField('reg_check');
		if ($check == 1) {
			$return['code'] = '-2';
			$this->ajaxReturn($return);
		}
		$where['id'] = $id;
		$data['reg_check'] = 1;
		M('Member')->where($where)->save($data);
		$arr = M('Member')->where(array('ai_member.id' => $id, 'ai_address.status' => 1))
			->field('ai_address.id,account,recommend_account,m_phone')
			->join('ai_address on ai_member.id = ai_address.s_id')
			->find();
		$datat['t_account'] = $arr['account'];
		$datat['t_recommend'] = $arr['recommend_account'];
		$datat['p_name'] = '真艾宝';
		$datat['t_amount'] = C('cwzdjhhs');
		$datat['t_phone'] = $arr['m_phone'];
		$datat['addr_id'] = $arr['id'];
		$datat['lc_check'] = 1;
		$datat['t_time'] = time();
		$tid = M('Thsq')->add($datat);
		if ($tid > 0) {
			$return['code'] = '1';
		} else {
			$return['code'] = '-1';
		}
		$this->ajaxReturn($return);
	}
	/**
	 * 总代注册拒绝
	 * @return [type] [description]
	 */
	public function registerRefuse() {
		$id = I('post.id');
		$data['refuse_time'] = time();
		$data['update_time'] = time();
		$bool = M('Member')->where(array('id' => $id))->save($data);
		$return['code'] = $bool > 0 ? 1 : -1;
		$this->ajaxReturn($return);
	}
	/**
	 * 联创 审核提货申请页面
	 * @return [type] [description]
	 */
	public function purchaseApplication() {
		$map['ai_thsq.update_time'] = array('eq', 0);
		$map['lc_check'] = array('eq', 0);
		$map['recommend_lc'] = session('account');
		$count = M('Thsq')
			->where($map)
			->join('ai_member on ai_thsq.t_account = ai_member.account')
			->count();
		$page = createPage($count, C('page_row'));
		$list = M('Thsq')
			->field('ai_thsq.id,t_account,t_amount,p_name,t_recommend,t_phone,s_addr,t_time')
			->where($map)
			->join('ai_member on ai_thsq.t_account = ai_member.account')
			->join('ai_address on ai_thsq.addr_id = ai_address.id')
			->select();
		$this->assign('purchase_list', $list);
		$assign = array(
			'count' => $count,
			'purchase_list' => $list,
			'page' => $page->show(),
		);
		$this->assign($assign);
		$this->display();
	}
	/**
	 * 提货申请同意
	 * @return [type] [description]
	 */
	public function passApplication() {
		$id = I('post.id');
		$bool = M('Thsq')->where(array('id' => $id))->setField('lc_check', 1);
		$return['code'] = $bool > 0 ? 1 : -1;
		$this->ajaxReturn($return);
	}
	/**
	 * 提货申请拒绝
	 * @return [type] [description]
	 */
	public function refuseApplication() {
		$id = I('post.id');
		$bool = M('Thsq')->where(array('id' => $id))->setField('lc_check', 2);
		$return['code'] = $bool > 0 ? 1 : -1;
		$this->ajaxReturn($return);
	}

}