<?
class CommentModel{
	static function validate($idValidator){
		Page::filterAndValidate(array(
			'title' => IVE::$in['title'],
			'text' => IVE::$in['text'],
			'_id' => $idValidator,
		));
		return !Page::errors();
	}
	static function checkComment($id,$table){
		$id = abs($id);
		if($id && Db::check($table,$id)){
			return true;
		}
		return false;
	}
	static function create($table){
		$insert = Arrays::extract(array('title','text','_id'),Page::$in);
		$insert['time_created'] = i()->Time()->datetime();
		$insert['significance'] = UserSignificance::get();
		$insert['enjoyment'] = 0;
		$insert['user_id'] = User::id();
		$insert['order_depth'] = 0;
		
		$parent = Page::$in['parent'];
		
		$lockName = 'commentAlter-'.$table.'-'.$insert['_id'];
		if(Lock::on($lockName,30)){
			if(self::checkComment($parent,$table)){
				$parent = Db::row($table,$parent,'order_in, order_depth, id');
				
				$insert['order_in'] = Db::row($table,array('self_id_parent'=>$parent['id']),'max(order_in)');
				if($insert['order_in']){//if children, find last child and add one to order_in
					$insert['order_in'] ++;
				}else{//else, add one to parent order_in
					$insert['order_in'] = $parent['order_in'] + 1;
				}
				$insert['order_depth'] = $parent['order_depth'] + 1;
				$insert['self_id_parent'] = $parent['id'];
			}else{
				list($found,$insert['order_in']) = Db::enumerate($table,array('_id'=>$insert['_id']),'id,max(order_in)');
				if($found){
					$insert['order_in'] ++;
				}else{
					$insert['order_in'] = 0;
				}
			}
			
			Db::query('update '.$table.' set order_in = order_in + 1 where order_in >= '.$insert['order_in']);
			$insert['id'] = Db::insert($table,$insert);
			
			//add vote by user for post
			$voteInsert = Arrays::extract(array('significance','enjoyment','time_created','user_id'),$insert);
			$voteInsert['_id'] = $insert['id'];
			Db::insert($table.'_vote',$voteInsert);
		
			Lock::off($lockName,30);
			
			//update comments stats for post
			MeanDeviation::comments($table,$insert['_id'],true);
			
			return $insert;
		}else{
			Page::error('Database too busy. Could not lock comment table');
		}
	}
	static function _delete($table,$id){
		$comment = Db::row($table,$id,'order_in, order_depth, _id');
		
		$lockName = 'commentAlter-'.$table.'-'.$comment['_id'];
		if(Lock::on($lockName,30)){
		
			//delete comment
			Db::delete($table,$id);
			
			//get child range
			$beyond = Db::row($table,array('order_in?>'=>$comment['order_in'],'order_depth?<='=>$comment['order_depth'],'_id'=>$comment['_id']),'min(order_in)');
			
			//delete children
			$deleteWhere = array(
					'_id' => $comment['_id'],
					'order_in?>'=>$comment['order_in']
				);
			if($beyond){
				$deleteWhere['order_in?<'] = $beyond;
			}
			Db::delete($table,$deleteWhere);
			
			$adjustment = $beyond - $comment['order_in'] - 1;
			if($adjustment > 0){
				//adjust other nodes
				Db::query('update '.$table.' set order_in = order_in - '.$adjustment.' where order_in > '.$comment['order_in']);
			}
			Lock::off($lockName,30);
		}else{
			Page::error('Database too busy. Could not lock comment table');
		}
	}
	static function read($table,$rowId){
		Page::$data->comments = Db::rows('select c.*, u.display_name user_name
			from '.$table.'_comment c left join user u on c.user_id = u.id
			where _id = '.$rowId.'
			order by order_in');
		
		Display::$json['commentStats'] = MeanDeviation::comments($table.'_comment',$rowId);
	}
}
