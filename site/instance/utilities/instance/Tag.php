<?
class Tag{
	static function parse($string){
		$string = preg_replace('@[^a-z,_\-0-9 \']@i','',$string);
		$potentialTags = preg_split('@\s*,\s*@',$string);
		$tags = array();
		foreach($potentialTags as $tag){
			if(trim($tag)){
				$tags[] = strtolower($tag);
			}
		}
		return $tags;
	}
	static function ids($tags){
		$ids = array();
		foreach($tags as $tag){
			$ids[] = Db::id('tag',array('text'=>$tag),'id');
		}
		return $ids;
	}
	static function associate($table,$itemId,$tags){
		$ids = self::ids($tags);
		$associationIds = array();
		foreach($ids as $id){
			$associationIds[] = Db::id($table,array('_id'=>$itemId,'tag_id'=>$id));
		}
		//delete old associations
		Db::delete($table,array(':id?not in'=>'('.implode(',',$associationIds).')','_id'=>$itemId));
	}
}
