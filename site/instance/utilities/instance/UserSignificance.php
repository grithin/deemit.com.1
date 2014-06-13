<?
class UserSignificance{
	static function get($userId=null){
		$userId = $userId ? $userId : User::id();
		return Db::row('select significance from user where id = '.$userId);
	}
	static function addReference($table,$rowId,$key,$significance,$reason,$userId=null){
		$userId = $userId ? $userId : User::id();
		$tableId = Db::id('table',array('name'=>$table));
		$insert = array(
				'table_id' => $tableId,
				'table_row_id' => $rowId,
				'key' => $key,
				'user_id' => $userId
			);
		$id = Db::insertIgnore('user_significance_log_reference',$insert);
		if($id){
			self::update($significance,$reason,$userId,$id);
		}
	}
	static function update($significance,$reason,$userId=null,$referenceId=null){
		$userId = $userId ? $userId : User::id();
		Db::update('user',array(':significance?='=>'significance + '.$significance),$userId);
		
		Db::insert('user_significance_log',array(
				'reason' => $reason,
				'significance' => $significance,
				'time_created' => i()->Time()->datetime(),
				'user_id'=>$userId,
				'reference_id' => $referenceId
			));
		$significance = Db::row('user',$userId,'significance');
		if($significance < 0){
			User::disable($userId);
		}
	}
}
