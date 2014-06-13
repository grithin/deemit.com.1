<?
class PageTool{
	static $table;
	static function validate(){
		Page::filterAndValidate(array(
			'_id' => '!v:isInteger,!p:checkId',
			'value' => '!v:existsAsValue|for;against',
		));
		return !Page::errors();
	}
	static function deleteObject($table,$id){
		$commentTable = $table.'_comment';
		Db::delete(self::$table,$id);
		Db::query('delete from c, cv
			using '.$commentTable.' c, '.$commentTable.'_vote cv
			where cv._id = c.id
				and c._id = '.$id);
		Db::delete($commentTable,array('_id'=>$id));
		Db::delete($table.'_vote',array('_id'=>$id));
		if($table == 'entity'){
			Db::delete($table.'_tag',array('_id'=>$id));
			//remove relations
			$entityRelations = Db::rows('entity_relation',array('_id_relatee'=>$id),'id');
			foreach($entityRelations as $relation){
				self::deleteObject('entity_relation',$relation['id']);
			}
			$entityRelations = Db::rows('entity_relation',array('_id_relater'=>$id),'id');
			foreach($entityRelations as $relation){
				self::deleteObject('entity_relation',$relation['id']);
			}
		}
	}
	
	static function create(){
		if(self::validate()){
			if(self::$table == 'user'){
				die('No voting on users yet');
			}
			$id = $insert['_id'] = Page::$in['_id'];
			
			$insert['time_created'] = i()->Time()->datetime();
			$insert['user_id'] = User::id();
			
			$column = 'significance';
			if(Page::$in['topic'] == 'enjoyment'){
				$column = 'enjoyment';
			}
			$insert[$column] = UserSignificance::get();
			
			$objectOwner = Db::row(self::$table,$id,'user_id');
			if(User::id() == $objectOwner){
				$isObjectOwner = true;
			}else{
				$insert[$column] = ceil($insert[$column]/10);
			}
			
			if(Page::$in['value'] != 'for'){
				$insert[$column] *= -1;
			}
			//owner may be deleting object
			if($insert['significance'] < 0){
				$votesUpon = Db::row(self::$table.'_vote',array('_id'=>$id),'count(*)');
				//user is trying to delete own object by voting against, so delete it
				if($isObjectOwner && $votesUpon <= 1){
					if(Page::$in['type'] == 'comment'){
						CommentModel::_delete(self::$table,$id);
					}else{
						self::deleteObject(self::$table,$id);	
					}
					Page::success('Object Deleted');
					return;
				}
			}
			
			$formerVote = Db::row(self::$table.'_vote',array('_id'=>$id,'user_id'=>User::id()),$column);
			if($formerVote == $insert[$column]){
				Page::error('Already voted this way');
				return;
			}
			
			$influence = $insert[$column] - $formerVote;
			
			Db::insertUpdate(self::$table.'_vote',$insert);
			Db::update(self::$table,array(':'.$column=>$column.' + '.$influence),$id);
			
//+	delete post if below 0 significance{
			if($column == 'significance'){
				$currentSignificance = Db::row(self::$table,$id,'significance');
				if($currentSignificance < 0){
					if(Page::$in['type'] == 'comment'){
						CommentModel::_delete(self::$table,$id);
						UserSignificance::update(-2,'Comment Deleted: '.$id,$objectOwner);
					}else{
						self::deleteObject(self::$table,$id);
						UserSignificance::update(-5,'Post Deleted: '.$id,$objectOwner);
					}
					Page::success('Object Deleted');
				}
			}
//+	}
			//update comments stats for post
			if(Page::$in['type'] == 'comment'){
				MeanDeviation::comments(self::$table,$id,true);
			}
			
			
			if($formerVote){
				Page::success('Vote Altered');
			}else{
				Page::success('Vote Added');
			}
			return true;
		}
	}
	static function checkId($value){
		if(Page::$in['type'] == 'comment'){
			self::$table .= '_comment';
		}
		if(!Db::check(self::$table,$value)){
			InputException::throwError('Could not find {_FIELD_} in DB');
		}
	}
}
