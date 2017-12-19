<?php
namespace Common\Controller;
use Think\Controller;

class CommonController extends Controller {
	public $server;
	public function __construct() {
		parent::__construct();
		Vendor('Sendsms.CCPRestSmsSDK');
		Vendor('phpqrcode.phpqrcode');
		Vendor('PHPExcel.Classes.PHPExcel.IOFactory', '', '.php');
		/** 引入PHPExcel */
		Vendor('PHPExcel.Classes.PHPExcel', '', '.php');
		$this->server = 'http://' . $_SERVER['HTTP_HOST'];
	}
}