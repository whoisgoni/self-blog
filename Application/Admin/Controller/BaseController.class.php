<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller {
	public function _initialize(){
		if(!A('Common')->is_login()){
			$this->error('您还没有登陆！', U('public/login'));
		}
	}
}
