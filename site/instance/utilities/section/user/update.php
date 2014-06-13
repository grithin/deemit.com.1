<?
class PageTool extends RelationModel{
	static $id;
	static function read(){
		$user = Page::$data->user = Db::row('user',self::$id);
		if($user){
			if($user['id'] != User::id()){
				Page::error('You are not the owner and can not edit');
			}
			return true;
		}
		return false;
	}
	static function validate(){
		Page::filterAndValidate(array(
			'public_statement' => Arrays::merge(array('v:filled|true'),Arrays::stringArray(IVE::$in['text'])),
		));
		return !Page::errors();
	}
	static function update(){
		if(self::validate()){
			$update = Arrays::extract(array('public_statement'),Page::$in);
			Db::update('user',$update,self::$id);
			return true;
		}
	}
}
