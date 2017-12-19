<?php
namespace Admin\Controller;
use Admin\Controller\PublicController;

class ArticleController extends PublicController {
	public function articleList() {
		$result = M('Notice')->select();
		$this->assign('notice', $result);
		$this->display();
	}
	public function articleAdd() {

		$this->display();
	}
	public function ArticleAssInfo() {
		$result = M('Notice')->add($_POST);
		echo json_encode($result);
	}
	//下架公告
	public function a_change_state() {
		$status = $_GET['status'];
		$id = $_GET['id'];
		$result = M('Notice')->where("id = {$id}")->save(array("status" => $status));
		echo json_encode($result);
	}
	//公告编辑页面
	public function changeArticle() {
		$id = $_GET['id'];
		$result = M('Notice')->where("id={$id}")->find();
		$this->assign('article', $result);
		$this->display();
	}
	//处理公告编辑
	public function changeArticleBegin() {
		$id = $_POST['id'];
		unset($_POST['id']);
		$result = M('Notice')->where("id={$id}")->save($_POST);
		echo json_encode($result);
	}
	//删除公告
	public function DelArticle() {
		$id = $_GET['id'];
		$result = M('Notice')->where("id={$id}")->delete();
		echo json_encode($result);
	}
	public function LotDelArticle() {
		$id = $_POST['id'];
		if (empty($id)) {$this->returnMessage('error', '选择要删除的数据');}
		$result = M('Notice')->where("id={$id}")->delete();
		if ($result > 0) {
			$this->returnMessage('success', '删除成功');
		} else {
			$this->returnMessage('success', '删除失败');
		}

	}
}