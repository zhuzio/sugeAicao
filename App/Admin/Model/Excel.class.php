<?php
namespace Admin\Model;
class Excel {
	protected $objPHPExcel;
	public function __construct() {
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('PRC');
		// 创建Excel文件对象
		$this->objPHPExcel = new \PHPExcel();
		// 设置文档信息，这个文档信息windows系统可以右键文件属性查看
		$this->objPHPExcel->getProperties()->setCreator("suge")
			->setLastModifiedBy("suge")
			->setTitle("Office 2007 XLSX Test Document")
			->setSubject("Office 2007 XLSX Test Document")
			->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("suge");
		//自动居中
		$this->objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		//宽度自适应
		$this->objPHPExcel->getActiveSheet()->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(0))->setAutoSize(true);
	}
	/**
	 * 导出联合创始人或总代信息
	 * @param  [type] $table [description]
	 * @return [type]        [description]
	 */
	public function memberExport($map) {

		//根据excel坐标，添加数据
		$this->objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()
			->setFormatCode(\PHPExcel_Cell_DataType::TYPE_STRING);

		$this->objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()
			->setFormatCode(\PHPExcel_Cell_DataType::TYPE_STRING);
		//设置宽度
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);

		//填入表头
		$this->objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
		$this->objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
		$this->objPHPExcel->getActiveSheet()->setCellValue('C1', '帐号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('D1', '账户余额');
		$this->objPHPExcel->getActiveSheet()->setCellValue('E1', '手机');
		$this->objPHPExcel->getActiveSheet()->setCellValue('F1', '身份证号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('G1', '加入时间');
		$arr = M('Member')->where($map)->select();
		$i = 1;
		foreach ($arr as $val) {
			$regtime = date("Y-m-d H:i:s", $val['reg_time']);
			$i++;
			$this->objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $i, $val['id'])
				->setCellValue('B' . $i, $val['name'])
				->setCellValue('C' . $i, $val['account'])
				->setCellValue('D' . $i, $val['balance'])
				->setCellValue('E' . $i, ' ' . $val['m_phone'] . ' ')
				->setCellValue('F' . $i, ' ' . $val['id_number'] . ' ')
				->setCellValue('G' . $i, $regtime);
		}

		// 重命名工作sheet
		$this->objPHPExcel->getActiveSheet()->setTitle('联合创始人');
		//设置E1F1单元格为文本

		// 设置第一个sheet为工作的sheet
		$this->objPHPExcel->setActiveSheetIndex(0);
		//点击下载
		$fileName = date('YmdHis', time());
		$this->download($fileName);

	}

	/**
	 * @return \PHPExcel
	 */
	public function download($fileName) {
		$objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		ob_end_clean(); //清除缓冲区,避免乱码
		$ua = $_SERVER["HTTP_USER_AGENT"];
		if (preg_match("/MSIE/", $ua)) {
			header('Content-Disposition: attachment; filename="' . $fileName . '.xlsx"');
		} else if (preg_match("/Firefox/", $ua)) {
			header('Content-Disposition: attachment; filename*="utf8\'\'' . $fileName . '.xlsx"');
		} else {
			header('Content-Disposition: attachment; filename="' . $fileName . '.xlsx"');
		}
		header("Content-Transfer-Encoding:binary");
		$objWriter->save('php://output');
	}
	public function txjlExport($str) {
		$this->objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()
			->setFormatCode(\PHPExcel_Cell_DataType::TYPE_STRING);

		$this->objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()
			->setFormatCode(\PHPExcel_Cell_DataType::TYPE_STRING);
		//设置宽度
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

		//填入表头
		$this->objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
		$this->objPHPExcel->getActiveSheet()->setCellValue('B1', '提现人姓名');
		$this->objPHPExcel->getActiveSheet()->setCellValue('C1', '提现金额');
		$this->objPHPExcel->getActiveSheet()->setCellValue('D1', '手机');
		$this->objPHPExcel->getActiveSheet()->setCellValue('E1', '提现银行卡');
		$this->objPHPExcel->getActiveSheet()->setCellValue('F1', '所属银行信息');
		$this->objPHPExcel->getActiveSheet()->setCellValue('G1', '提款日期');
		$this->objPHPExcel->getActiveSheet()->setCellValue('H1', '审核状态');
		$bankList = require_once ROOT_PATH . 'bankList.php';
		if ($str == 'noupdate') {
			$where['ai_txjl.update_time'] = array('eq', 0);
		} else {
			$where['ai_txjl.update_time'] = array('gt', 0);
		}
		$list = M('Txjl')
			->field('ai_txjl.id,ai_txjl.pass_time,ai_txjl.refause_time,ai_txjl.update_time,t_yhk,t_money,m_phone,name,t_time')
			->join('ai_member on ai_txjl.t_id = ai_member.id')
			->where($where)
			->select();
		$arr = [];
		foreach ($list as $val) {
			$val['ssyh'] = bankInfo($val['t_yhk'], $bankList);
			$arr[] = $val;
		}
		$i = 1;
		foreach ($arr as $val) {
			if ($val['update_time'] == 0) {
				$status = '待审核';
			} elseif ($val['pass_time'] > 0) {
				$status = '已通过';
			} else {
				$status = '已拒绝';
			}
			$regtime = date("Y-m-d H:i:s", $val['t_time']);
			$i++;
			$this->objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $i, $val['id'])
				->setCellValue('B' . $i, $val['name'])
				->setCellValue('C' . $i, $val['t_money'])
				->setCellValue('D' . $i, ' ' . $val['m_phone'] . ' ')
				->setCellValue('E' . $i, ' ' . $val['t_yhk'] . ' ')
				->setCellValue('F' . $i, $val['ssyh'])
				->setCellValue('G' . $i, $regtime)
				->setCellValue('H' . $i, $status);
		}

		// 重命名工作sheet
		$this->objPHPExcel->getActiveSheet()->setTitle('提现记录');
		//设置E1F1单元格为文本

		// 设置第一个sheet为工作的sheet
		$this->objPHPExcel->setActiveSheetIndex(0);
		//点击下载
		$fileName = date('YmdHis', time());
		$this->download($fileName);
	}
	public function thsqExport($str) {
		$this->objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()
			->setFormatCode(\PHPExcel_Cell_DataType::TYPE_STRING);

		//设置宽度
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(70);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);

		//填入表头
		$this->objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
		$this->objPHPExcel->getActiveSheet()->setCellValue('B1', '提货人姓名');
		$this->objPHPExcel->getActiveSheet()->setCellValue('C1', '提货人帐号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('D1', '产品名称');
		$this->objPHPExcel->getActiveSheet()->setCellValue('E1', '提货数量');
		$this->objPHPExcel->getActiveSheet()->setCellValue('F1', '提货人手机号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('G1', '收货地址');
		$this->objPHPExcel->getActiveSheet()->setCellValue('H1', '申请日期');
		$this->objPHPExcel->getActiveSheet()->setCellValue('I1', '审核状态');
		$this->objPHPExcel->getActiveSheet()->setCellValue('J1', '是否发货');
		if ($str == 'application') {
			$map['ai_thsq.update_time'] = array('eq', 0);
		} else if ($str == '1') {
			$map['ai_thsq.pass_time'] = array('gt', 0);
			$map['ai_thsq.is_fare_add'] = array('eq', 0);
		} else if ($str == '2') {
			$map['ai_thsq.pass_time'] = array('gt', 0);
			$map['ai_thsq.is_fare_add'] = array('eq', 1);
		} else {
			$map['ai_thsq.pass_time'] = array('gt', 0);
		}
		$arr = M('Thsq')
			->field('ai_thsq.id,ai_thsq.pass_time,ai_thsq.refuse_time,p_name,ai_thsq.update_time,is_fare_add,t_amount,t_account,s_addr,ai_thsq.t_time,t_phone,name')
			->where($map)
			->join('ai_member on ai_thsq.t_account = ai_member.account')
			->join('ai_address on ai_thsq.addr_id = ai_address.id')
			->select();
		$i = 1;
		foreach ($arr as $val) {
			if ($val['update_time'] == 0) {
				$status = '待审核';
			} elseif ($val['pass_time'] > 0) {
				$status = '已通过';
			} else {
				$status = '已拒绝';
			}
			$is_fare_add = $val['is_fare_add'] == 1 ? '已发货' : '未发货';
			$time = date("Y-m-d H:i:s", $val['t_time']);
			$i++;
			$this->objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $i, $val['id'])
				->setCellValue('B' . $i, $val['name'])
				->setCellValue('C' . $i, $val['t_account'])
				->setCellValue('D' . $i, $val['p_name'])
				->setCellValue('E' . $i, $val['t_amount'])
				->setCellValue('F' . $i, ' ' . $val['t_phone'] . ' ')
				->setCellValue('G' . $i, $val['s_addr'])
				->setCellValue('H' . $i, $time)
				->setCellValue('I' . $i, $status)
				->setCellValue('J' . $i, $is_fare_add);
		}

		// 重命名工作sheet
		$this->objPHPExcel->getActiveSheet()->setTitle('提货记录');
		//设置E1F1单元格为文本

		// 设置第一个sheet为工作的sheet
		$this->objPHPExcel->setActiveSheetIndex(0);
		//点击下载
		$fileName = date('YmdHis', time());
		$this->download($fileName);
	}
	public function fhjlExport() {
		$this->objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()
			->setFormatCode(\PHPExcel_Cell_DataType::TYPE_STRING);

		//设置宽度
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);

		//填入表头
		$this->objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
		$this->objPHPExcel->getActiveSheet()->setCellValue('B1', '分红人帐号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('C1', '分红人姓名');
		$this->objPHPExcel->getActiveSheet()->setCellValue('D1', '贡献人帐号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('E1', '贡献人姓名');
		$this->objPHPExcel->getActiveSheet()->setCellValue('F1', '分红人手机号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('G1', '分红前账户余额');
		$this->objPHPExcel->getActiveSheet()->setCellValue('H1', '流水');
		$this->objPHPExcel->getActiveSheet()->setCellValue('I1', '分红后账户余额');
		$this->objPHPExcel->getActiveSheet()->setCellValue('J1', '分红说明');
		$this->objPHPExcel->getActiveSheet()->setCellValue('K1', '生成时间');
		//查询记录
		$list = M('Fhjl')
			->field('ai_fhjl.id,ai_fhjl.account,ai_fhjl.contribute_account,ai_fhjl.contribute_name,desc,total,m_phone,name,plus,ai_fhjl.balance,create_time')
			->join('ai_member on ai_fhjl.account = ai_member.account')
			->select();
		$i = 1;
		foreach ($list as $val) {
			$createtime = date("Y-m-d H:i:s", $val['create_time']);
			$i++;
			$this->objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $i, $i)
				->setCellValue('B' . $i, $val['account'])
				->setCellValue('C' . $i, $val['name'])
				->setCellValue('D' . $i, $val['contribute_account'])
				->setCellValue('E' . $i, $val['contribute_name'])
				->setCellValue('F' . $i, ' ' . $val['m_phone'] . ' ')
				->setCellValue('G' . $i, $val['total'])
				->setCellValue('H' . $i, $val['plus'])
				->setCellValue('I' . $i, $val['balance'])
				->setCellValue('J' . $i, $val['desc'])
				->setCellValue('K' . $i, $createtime);
		}

		// 重命名工作sheet
		$this->objPHPExcel->getActiveSheet()->setTitle('账户明细');
		//设置E1F1单元格为文本

		// 设置第一个sheet为工作的sheet
		$this->objPHPExcel->setActiveSheetIndex(0);
		//点击下载
		$fileName = date('YmdHis', time());
		$this->download($fileName);
	}
	public function fareExport() {

		//设置宽度
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);

		//填入表头
		$this->objPHPExcel->getActiveSheet()->setCellValue('A1', '编号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('B1', '联合创始人帐号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('C1', '联合创始人姓名');
		$this->objPHPExcel->getActiveSheet()->setCellValue('D1', '提货人帐号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('E1', '提货人姓名');
		$this->objPHPExcel->getActiveSheet()->setCellValue('F1', '运单号');
		$this->objPHPExcel->getActiveSheet()->setCellValue('G1', '运费');
		$this->objPHPExcel->getActiveSheet()->setCellValue('H1', '是否扣除');
		$this->objPHPExcel->getActiveSheet()->setCellValue('I1', '生成时间');
		$list = M('Fare')->select();
		$i = 1;
		foreach ($list as $val) {
			$createtime = date("Y-m-d H:i:s", $val['create_time']);
			$status = $val['is_process'] == 0 ? '未扣除' : '已扣除';
			$i++;
			$this->objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $i, $val['id'])
				->setCellValue('B' . $i, $val['lc_account'])
				->setCellValue('C' . $i, $val['lc_name'])
				->setCellValue('D' . $i, $val['purchase_account'])
				->setCellValue('E' . $i, $val['purchase_name'])
				->setCellValue('F' . $i, $val['waybill_number'])
				->setCellValue('G' . $i, $val['fare'])
				->setCellValue('H' . $i, $status)
				->setCellValue('I' . $i, $createtime);
		}

		// 重命名工作sheet
		$this->objPHPExcel->getActiveSheet()->setTitle('运费明细');
		//设置E1F1单元格为文本

		// 设置第一个sheet为工作的sheet
		$this->objPHPExcel->setActiveSheetIndex(0);
		//点击下载
		$fileName = date('YmdHis', time());
		$this->download($fileName);
	}
}
