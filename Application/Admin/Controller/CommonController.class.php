<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function is_login(){
		$admin_member_id = isset($_SESSION['admin_member']['id']) ? intval($_SESSION['admin_member']['id']) : 0;
		$is_login = isset($_SESSION['admin_member']['is_login']) ? intval($_SESSION['admin_member']['is_login']) : 0;
		if($admin_member_id == 0 || $is_login == 0){
			return false;
		}
		return true;
	}
	public function post2get(){
		$sub_url = '';
		foreach($_POST as $key=>$val){
			$sub_url .= '&' . $key . '=' . urlencode($val);
		}
		redirect(U() . $sub_url);
	}
}
