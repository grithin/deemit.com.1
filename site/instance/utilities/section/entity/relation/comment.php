<?
class PageTool extends RelationModel{
	static $type = 'relation';
	static $renderer = array('DH','entityRelation');
	static function read(){
		$object = abs(Page::$in['_id']);
		if($object){
			Page::$data->object = Db::row('entity_relation',$object,'reason_title title, id');
		}
		if(!Page::$data->object){
			error('No object id provided');
		}
	}
	static function validate(){
		return CommentModel::validate(self::$validators['relation']);
	}
	static function create(){
		if(self::validate()){
			return CommentModel::create('entity_relation_comment');
		}
	}
}
