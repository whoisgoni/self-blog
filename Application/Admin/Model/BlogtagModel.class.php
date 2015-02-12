<?php
namespace Admin\Model;
use Think\Model;
class BlogtagModel extends Model {

	protected $trueTableName = 'blog_tag';

	public function insert_tags_return_tagids($tags){
		if(!is_array($tags)){
			$tags = explode(',', str_replace('，',',',$tags));
		}
		$tags = array_unique($tags);
		$tagids = array();
		foreach($tags as $tag){
			if($tag === ''){
				continue;
			}
			$tag = strtolower($tag);
			$id = $this->where(array('word'=>$tag))->getField('id');
			if($id){
				$tagids[] = $id;
			}else{
				$id = $this->add(array('word'=>$tag));
				$tagids[] = $id;
			}
			
		}
		$tagids = array_unique($tagids);
		return $tagids;
	}
}

