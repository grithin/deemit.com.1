<?
class PageTool extends UserModel{
	static $type = 'user';
	static $renderer = array('DH','user');
	static function read(){
		$object = abs(Page::$in['_id']);
		if($object){
			Page::$data->object = Db::row('user',$object,'display_name title, id');
		}
		if(!Page::$data->object){
			error('No object id provided');
		}
	}
	static function validate(){
		return CommentModel::validate(self::$validators['user']);
	}
	static function create(){
		if(self::validate()){
			return CommentModel::create('user_comment');
		}
	}
}
