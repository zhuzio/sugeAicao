<?php
namespace Admin\Controller;
use Admin\Controller\PublicController;
use Admin\Model\Excel;

class ManageController extends PublicController {
	public function __construct() {
		parent::__construct();
	}
	/**
	 * 平台比例设置
	 * @return [type] [description]
	 */
	public function dividedSet() {
		$settings = include APP_PATH . 'Common/Conf/settings.php';
		$this->assign('settings', $settings);
		$this->display();
	}
	/**
	 * 平台设置处理逻辑
	 * @return [type] [description]
	 */
	public function dividedSetAction() {
		$file_length = writeSettings(I('post.'));
		if ($file_length) {
			$this->success('保存成功！');
		} else {
			$this->error('保存失败！请检查文件权限');
		}

	}
	/**
	 * 意见反馈列表
	 * @return [type] [description]
	 */
	public function opinion() {
		$list = M('Opinion')->select();
		$this->assign('op_list', $list);
		$this->display();
	}
	/**
	 * 意见反馈删除
	 * @return [type] [description]
	 */
	public function opinionDel() {
		$res = M('Opinion')->delete(I('get.id'));
		$this->display();
	}
	/**
	 * 产品类型列表
	 * @return [type] [description]
	 */
	public function typeList() {
		$typeList = M('Type')->select();
		$this->assign('type_list', $typeList);
		$this->display();
	}
	/**
	 * 产品类型编辑
	 * @return [type] [description]
	 */
	public function typeEdit() {
		$type = M('Type')->where(array('id' => I('get.id')))->find();
		$this->assign('arr', $type);
		$this->display();
	}
	/**
	 * 产品类型编辑处理
	 * @return [type] [description]
	 */
	public function typeEditAction() {
		$data['p_name'] = I('post.type_name');
		$id = M('Type')->where(array('id' => I('post.id')))->save($data);
		if ($id > 0) {
			$this->success('修改成功！');
		} else {
			$this->error('修改失败！');
		}
	}
	/**
	 * 类型删除
	 * @return [type] [description]
	 */
	public function typeDel() {
		$res = M('Type')->delete(I('get.id'));
		if ($res > 0) {
			$this->success('删除成功!');
		} else {
			$this->error('删除失败!');
		}
	}
	/**
	 * 产品类型添加页面
	 * @return [type] [description]
	 */
	public function typeAdd() {
		$this->display();
	}
	/**
	 * 添加类型处理
	 * @return [type] [description]
	 */
	public function typeAddAction() {
		$data['p_name'] = I('post.type_name');
		$id = M('Type')->add($data);
		if ($id > 0) {
			$this->success('添加成功！');
		} else {
			$this->error('添加失败！');
		}
	}
	/**
	 * 数据库备份，写上了但没有做
	 */
	public function Backup() {
		if (!IS_POST) {
			return;
		}
		$dir = $_SERVER["DOCUMENT_ROOT"] . "/db_backup/";
		if (!is_dir($dir)) {
			mkdir($dir);
		}
		$filename = date("YmdHis", time());
		$cmd = '"/usr/local/mysql/bin/mysqldump" -u' . C("DB_USER") . ' -p' . C("DB_PWD") . ' ' . C("DB_NAME") . ' > ' . $dir . $filename . '.sql';
		exec($cmd, $output, $status);
		if ($status) {
			$this->error("备份失败");
		} else {
			$this->success("备份成功");
		}
		$list = glob($dir . "*.sql");
		foreach ($list as $k => $v) {
			$v1 = explode(".", $v);
			$time = strtotime(str_replace($dir, '', $v1[0]));
			if (strlen($time) != 10) {
				continue;
			}
			$list[$k] = date("Y-m-d H:i:s", $time);
		}
		$this->assign("list", $list);
		$this->display("index/data");
	}
	/**
	 * 导出联合创始人
	 * @return [type] [description]
	 */
	public function lcexport() {
		$excelObj = new Excel();
		$map['is_lc'] = 1;
		$map['delete_time'] = array('eq', 0);
		$excelObj->memberExport($map);
	}
	/**
	 * 导出总代理
	 * @return [type] [description]
	 */
	public function zdexport() {
		$excelObj = new Excel();
		$map['is_lc'] = array('eq', 0);
		$map['is_zd'] = array('eq', 1);
		$map['delete_time'] = array('eq', 0);
		$excelObj->memberExport($map);
	}
	/**
	 * 导出提现记录 已处理
	 * @return [type] [description]
	 */
	public function txjlexport() {
		$excelObj = new Excel();
		$excelObj->txjlExport('already');
	}
	/**
	 * 导出提现记录 未处理
	 * @return [type] [description]
	 */
	public function txjlexport1() {
		$excelObj = new Excel();
		$excelObj->txjlExport('noupdate');
	}
	/**
	 * 导出提货记录（已处理）
	 * @return [type] [description]
	 */
	public function thsqexport() {
		$excelObj = new Excel();
		$excelObj->thsqExport('already');
	}
	/**
	 * 导出提货记录（已处理未发货的）
	 * @return [type] [description]
	 */
	public function thsqExport1() {
		$excelObj = new Excel();
		$excelObj->thsqExport('1');
	}
	/**
	 * 导出提货记录（已处理已发货的）
	 * @return [type] [description]
	 */
	public function thsqExport2() {
		$excelObj = new Excel();
		$excelObj->thsqExport('2');
	}
	/**
	 * 导出提货记录（通过提货申请的all）
	 *
	 * @return [type] [description]
	 */
	public function thsqExportAll() {
		$excelObj = new Excel();
		$excelObj->thsqExport('all');
	}
	/**
	 * 导出提货申请记录（未处理）
	 * @return [type] [description]
	 */
	public function applicationExport() {
		$excelObj = new Excel();
		$excelObj->thsqExport('application');
	}
	/**
	 * 导出账户明细
	 * @return [type] [description]
	 */
	public function fhjlExport() {
		$excelObj = new Excel();
		$excelObj->fhjlExport();
	}
	/**
	 * 导出运费单
	 * @return [type] [description]
	 */
	public function fareExport() {
		$excelObj = new Excel();
		$excelObj->fareExport();
	}
	/**
	 * 检查帐号是否存在
	 * @return [type] [description]
	 */
	public function checkAccount() {
		$return = D('Member')->check_account(I('post.account'));
		$this->ajaxReturn($return);
	}
	/**
	 * 手动添加运费单
	 * @return [type] [description]
	 */
	public function fareAdd() {
		$this->display();
	}
	/**
	 * 手动添加运费单
	 * @return [res] [description]
	 */
	public function fareAddAction() {
		$post = I('post.');
		$lc_info = M('Member')->field('account,id,name,balance')->where(array('account' => $post['lc_account']))->find();
		$purchase_info = M('Member')->field('account,id,name')->where(array('account' => $post['purchase_account']))->find();
		$data['lc_id'] = $lc_info['id'];
		$data['lc_account'] = $lc_info['account'];
		$data['lc_name'] = $lc_info['name'];
		$data['purchase_id'] = $purchase_info['id'];
		$data['purchase_account'] = $purchase_info['account'];
		$data['purchase_name'] = $purchase_info['name'];
		$data['thsq_amount'] = $post['thsq_amount'];
		$data['waybill_number'] = $post['waybill_number'];
		$data['fare'] = $post['fare'];
		$data['create_time'] = time();
		if ($post['is_process'] = 1) {
			if ($post['fare'] < $lc_info['balance']) {
				$bool = M('Member')->where(array('id' => $lc_info['id']))->setDec('balance', $post['fare']);
				if ($bool !== false) {
					$data_fhjl['account'] = $lc_info['account'];
					$data_fhjl['total'] = $lc_info['balance'];
					$data_fhjl['plus'] = '-' . $post['fare'];
					$data_fhjl['balance'] = $lc_info['balance'] - $post['fare'];
					$data_fhjl['desc'] = '运费-' . $post['fare'];
					$data_fhjl['type'] = 3;
					$data_fhjl['create_time'] = time();
					M('Fhjl')->add($data_fhjl);
				}
				$data['is_process'] = $bool ? 1 : 0;
			}
		}
		$new = M('Fare')->add($data);
		if ($new > 0) {
			$this->success('添加成功');
		} else {
			$this->error('添加失败，请检查网络！');
		}
	}
	//提货列表里面添加运费单页面
	public function fareAdd1() {
		$purchase_info = M('Member')->where(array('account' => I('get.account')))->find();
		$lc_info = M('Member')->where(array('account' => $purchase_info['recommend_lc']))->find();
		$thsq_amount = M('Thsq')->where(array('id' => I('get.id')))->getField('t_amount');
		$assign = array(
			'thsq_amount' => $thsq_amount,
			'thsq_id' => I('get.id'),
			'purchase_info' => $purchase_info,
			'lc_info' => $lc_info);
		$this->assign($assign);
		$this->display();
	}
	/**
	 * 提货列表里面添加运费单处理
	 * @return [type] [description]
	 */
	public function fareAdd1Action() {
		$post = I('post.');
		$post['create_time'] = time();
		$new = M('Fare')->add($post);
		$return['code'] = $new > 0 ? 1 : -1;
		if ($return['code'] == 1) {
			M('Thsq')->where(array('id' => $post['thsq_id']))->setField('is_fare_add', 1);
		}
		$this->ajaxReturn($return);
	}
	//运费单编辑页面
	public function fareEdit() {
		$fare = M('Fare')->where(array('id' => I('get.id')))->find();
		$this->assign('fare', $fare);
		$this->display();
	}
	/**
	 * 运费单编辑处理
	 * @return [ajax] [code 1 : -1]
	 */
	public function fareEditAction() {
		$post = I('post.');
		$bool = M('Fare')->where(array('id' => I('post.id')))->save($post);
		$return['code'] = $bool !== false ? 1 : -1;
		$this->ajaxReturn($return);
	}
	/**
	 * 运费单删除
	 * @return [ajax] [code 1 : -1]
	 */
	public function fareDel() {
		$res = M('Fare')->where(array('id' => I('post.id')))->delete();
		$return['code'] = $res > 0 ? 1 : -1;
		$this->ajaxReturn($return);
	}
	/**
	 * 扣款处理
	 * @return [type] [description]
	 */
	public function fareProcess() {
		if (!IS_POST) {
			return;
		}
		$id = I('post.id');
		$fare_info = M('Fare')->where(array('id' => $id))->find();
		$lc_info = M('Member')->where(array('account' => $fare_info['lc_account']))->find();
		if ($fare_info['fare'] < $lc_info['balance']) {
			$bool = M('Member')->where(array('id' => $lc_info['id']))->setDec('balance', $fare_info['fare']);
			if ($bool !== false) {
				M('Fare')->where(array('id' => $fare_info['id']))->setField('is_process', 1);
				$data_fhjl['account'] = $lc_info['account'];
				$data_fhjl['total'] = $lc_info['balance'];
				$data_fhjl['plus'] = '-' . $fare_info['fare'];
				$data_fhjl['balance'] = $lc_info['balance'] - $fare_info['fare'];
				$data_fhjl['desc'] = '运费-' . $fare_info['fare'];
				$data_fhjl['type'] = 3;
				$data_fhjl['create_time'] = time();
				M('Fhjl')->add($data_fhjl);
			}
			$return['code'] = '1';
			$return['msg'] = '扣除成功！';
		} else {
			$return['code'] = '-1';
			$return['msg'] = '余额不足！';
		}
		$this->ajaxReturn($return);
	}
	/**
	 * 未扣款的做统一处理
	 * @return [type] [description]
	 */
	public function fareAllProcess() {
		$where['is_process'] = array('eq', 0);
		$no_process = M('Fare')->where(array('is_process' => 0))->select();
		foreach ($no_process as $key => $value) {
			$lc_info = M('Member')->where(array('id' => $value['lc_id']))->find();
			if ($lc_info['balance'] >= $value['fare']) {
				$bool = M('Member')->where(array('id' => $value['lc_id']))->setDec('balance', $value['fare']);
				if ($bool !== false) {
					M('Fare')->where(array('id' => $value['id']))->setField('is_process', 1);
					$data_fhjl['account'] = $lc_info['account'];
					$data_fhjl['total'] = $lc_info['balance'];
					$data_fhjl['plus'] = '-' . $value['fare'];
					$data_fhjl['balance'] = $lc_info['balance'] - $value['fare'];
					$data_fhjl['desc'] = '运费-' . $value['fare'];
					$data_fhjl['type'] = 3;
					$data_fhjl['create_time'] = time();
					M('Fhjl')->add($data_fhjl);
				}
			}
		}
		$return['code'] = '1';
		$this->ajaxReturn($return);
	}
	/**
	 * 运费单列表（all）
	 * @return [type] [description]
	 */
	public function fareList() {
		$count = M('Fare')->count();
		$Page = createPage($count, 10);
		$show = $Page->show(); // 分页显示输出
		// 进行分页数据查询
		$list = M('Fare')
			->order('create_time desc')
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$assign = array(
			'count' => $count,
			'fare_list' => $list,
			'show' => $show,
		);
		$this->assign($assign);
		$this->display();
	}

}