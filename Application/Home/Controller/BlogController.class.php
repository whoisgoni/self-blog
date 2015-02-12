<?php
namespace Home\Controller;
use Think\Controller;
class BlogController extends Controller {
    public function index(){
		$count = D()->query("select count(1) FROM `blog_article` where `status` = 3");
		$count = $count[0]['count(1)'];
		$page = new \Think\PageBootstrap($count, 15);
		$list = D()->query("select * from `blog_article` where `status` = 3 order by `id` desc limit {$page->firstRow},{$page->listRows}");
		foreach($list as $key=>$val){
			$tag_list = D()->query("select `word` from `blog_tag` where `id` in (select `blog_tag_id` from `blog_article_tag` where `blog_article_id` = {$val['id']})");
			foreach($tag_list as $val){
				$list[$key]['tags'][] = $val['word'];
			}
		}
		$this->assign('list',$list);
		$this->assign('page',$page->show());
		$this->display();
    }
	public function detail(){
		$id = I('get.id', 0);
		if(!$id){
			$this->error('参数错误', U('blog/index'));
		}
		$res = M()->table('blog_article')->where(array('id'=>$id,'status'=>3))->find();
		if(!$res){
			$this->error('数据错误', U('blog/index'));
		}
		$tag_list = M()->query("select `word` from `blog_tag` where `id` in (select `blog_tag_id` from `blog_article_tag` where `blog_article_id` = {$id})");
		foreach($tag_list as $val){
			$res['tags'][] = $val['word'];
		}
		$this->assign('res', $res);
		$this->display();
	}
}
