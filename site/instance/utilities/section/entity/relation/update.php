<?
class PageTool extends RelationModel{
	static $id;
	static function read(){
		$relation = Page::$data->relation = Db::row('select r.*, u.display_name user_name
			from entity_relation r left join user u on r.user_id = u.id
			where r.id = '.self::$id);
		if($relation){
			if($relation['user_id'] != User::id()){
				Page::error('You are not the owner and can not edit');
			}
			return true;
		}
		return false;
	}
	static function validate(){
		Page::filterAndValidate(array(
			'reason_text' => IVE::$in['text'],
		));
		return !Page::errors();
	}
	static function update(){
		if(self::validate()){
			$update = Arrays::extract(array('reason_text'),Page::$in);
			Db::update('entity_relation',$update,self::$id);
			return true;
		}
	}
}
