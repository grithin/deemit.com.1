<?
class PageTool extends RelationModel{
	static $id;
	static function read(){
		$user = Page::$data->user = Db::row('user',self::$id);
		if($user){
			return true;
		}
		return false;
	}
	static function validate(){
		Page::filterAndValidate(array(
			'reason' => Arrays::stringArray(IVE::$in['text']),
		));
		return !Page::errors();
	}
	static function create(){
		if(self::validate()){
			$insert = Arrays::extract(array('reason'),Page::$in);
			$insert['__id_requester'] = User::id();
			$insert['__id_target'] = self::$id;
			$insert['time_created'] = i()->Time()->datetime();
			$insert['significance'] = UserSignificance::get();
			Db::insertUpdate('user_request_deletion',$insert);
			return true;
		}
	}
}
