<?php
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller {
    public function login(){
		if(IS_POST){
			$name = I('post.name', '');
			$password = I('post.password', '');
			$verify = I('post.verify', '');
			if(!$name || !$password || !$verify){
				$this->error('请填写完整！');
			}
			if(!$this->check_verify($verify)){
				$this->error('验证码错误！');
			}
			$password = md5(sha1($password));
			$res = D()->query("select * from `admin_member` where `name` = '%s' and `password` = '%s'", $name, $password);
			if(!$res){
				$this->error('用户名或密码错误！');
			}
			$_SESSION['admin_member'] = array(
				'id' => $res[0]['id'],
				'name' => $res[0]['name'],
				'is_login' => 1,
			);
			$this->success('登陆成功！', U('index/index'));
		}else{
			if(A('Common')->is_login()){
				$this->redirect('index/index');
			}
			$this->display();
		}
    }
	public function logout(){
		$_SESSION['admin_member'] = null;
		$this->success('退出成功！', U('login'));
	}
	public function verify(){
		$Verify = new \Think\Verify();
		$Verify->entry();
	}
	function check_verify($code, $id = ''){
		$verify = new \Think\Verify();
		return $verify->check($code, $id);
	}
}
