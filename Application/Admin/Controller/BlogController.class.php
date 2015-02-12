<?php
namespace Admin\Controller;
class BlogController extends BaseController {
    public function index(){
		if(IS_POST){
			A('Common')->post2get();
		}
		$map = array();
		$where = '';
		$keyword = I('get.keyword', '');
		if($keyword !== ''){
			if(preg_match("/^\d+$/",$keyword)){
				$map[] = "`id` = {$keyword}";
			}else{
				$map[] = "`title` like '%{$keyword}%'";
			}
		}
		$status = I('get.status', '');
		if(in_array($status, array(1,2,3))){
			$map[] = "`status` = {$status}";
		}
		if($map){
			$where = 'where ' . implode(' and ', $map);
		}
		$count = D()->query("select count(1) FROM `blog_article` {$where}");
		$count = $count[0]['count(1)'];
		$page = new \Think\PageBootstrap($count, 10);
		$list = D()->query("select * from `blog_article` {$where} order by `id` desc limit {$page->firstRow},{$page->listRows}");
		$this->assign('list',$list);
		$this->assign('page',$page->show());
		$this->display();
    }
	public function add(){
		if(IS_POST){
			$title = I('post.title', '');
			if(!$title){
				$this->error('标题不能为空');
			}
			$content = I('post.content', '', '');
			if(!$content){
				$this->error('内容不能为空');
			}
			$tags = I('post.tags', '');
			$status = I('post.status', 0);
			$data = array(
				'title' => $title,		
				'content' => $content,		
				'status' => $status,		
				'add_time' => time(),		
			);
			$id = M()->table('blog_article')->add($data);
			$tagids = D('Blogtag')->insert_tags_return_tagids($tags);
			$article_tag_data = array();
			foreach($tagids as $tag_id){
				$article_tag_data[] = array(
					'blog_article_id' => $id,
					'blog_tag_id' => $tag_id,
				);
			}
			if($article_tag_data){
				M()->table('blog_article_tag')->addall($article_tag_data);
			}
			$this->success('添加成功', U('blog/index'));
			exit;
		}
		$this->display();
	}
	public function edit(){
		$id = I('param.id', 0);
		if(!$id){
			$this->error('参数错误');
		}
		if(IS_POST){
			$title = I('post.title', '');
			if(!$title){
				$this->error('标题不能为空');
			}
			$content = I('post.content', '', '');
			if(!$content){
				$this->error('内容不能为空');
			}
			$tags = I('post.tags', '');
			$status = I('post.status', 0);
			$data = array(
				'id' => $id,
				'title' => $title,
				'content' => $content,
				'status' => $status,
				'last_edit_time' => time(),
			);
			M()->table('blog_article')->save($data);
			$tagids = D('Blogtag')->insert_tags_return_tagids($tags);
			$article_tag_data = array();
			foreach($tagids as $tag_id){
				$article_tag_data[] = array(
					'blog_article_id' => $id,
					'blog_tag_id' => $tag_id,
				);
			}
			M()->table('blog_article_tag')->where(array('blog_article_id'=>$id))->delete();
			if($article_tag_data){
				M()->table('blog_article_tag')->addall($article_tag_data);
			}
			$this->success('编辑成功', U('blog/index'));
			exit;
		}
		$res = M()->table('blog_article')->where(array('id'=>$id))->find();
		if(!$res){
			$this->error('数据错误', U('blog/index'));
		}
		$tag_res = M()->query("select `word` from `blog_tag` where `id` in (select `blog_tag_id` from `blog_article_tag` where `blog_article_id` = {$id})");
		foreach($tag_res as $val){
			$tags[] = $val['word'];
		}
		$res['tags'] = implode(',', $tags);
		$this->assign('res', $res);
		$this->display();
	}
}
