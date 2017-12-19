<?php
namespace Admin\Controller;
use Admin\Controller\PublicController;
use Admin\Model\AdminModel;

class IndexController extends PublicController {
	protected $adminModel;
	public function __construct() {
		parent::__construct();
		$this->adminModel = new AdminModel();
	}
	/*
    *首页面 展示所属权限*/
	public function index() {
		/*
		获取角色名称*/
		$info = $this->adminModel->getAdminInfo(session('adminId'));
		/*
        获取所有顶级权限 pid=0*/
		$arr1 = $this->adminModel->getAuth1();
		/*
        获取所有顶级权限下的权限*/
		$arr2 = $this->adminModel->getAuth2();
		$assign = array(
			'info' => $info,
			'arr1' => $arr1,
			'arr2' => $arr2,
		);
		$this->assign($assign);
		$this->display();
	}
	public function welcome() {
		@exec("ipconfig /all", $array);
		$getstr = explode(":", $array[3]);
		$this->assign("pcname", $getstr[1]);
		/* END*/
		/*系统名*/
		$sessionid = session_id();
		$this->assign('sessionid', $sessionid);
		$this->assign("systemname", PHP_OS);
		$this->display();
	}
}