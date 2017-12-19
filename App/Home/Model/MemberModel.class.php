<?php
namespace Home\Model;
use Common\Model\BaseModel;

class MemberModel extends BaseModel {
	public function __construct() {
		parent::__construct();
	}
	public function getInfo($id) {
		return $this->memberObj->where(array('id' => $id))->find();
	}
	public function getSmsCode($phone) {
		$code = mt_rand(1000, 9999);
		session('sms_code' . $phone, $code);
		return sendTemplateSMS($phone, array($code, '5分钟'), '211785');
	}

}