<?
class PageTool extends EntityModel{
	static $type = 'entity';
	static $renderer = array('DH','entity');
	
	static function read(){
		$object = abs(Page::$in['_id']);
		if($object){
			Page::$data->object = Db::row('entity',$object,'title, id');
		}
		if(!Page::$data->object){
			error('No object id provided');
		}
	}
	static function validate(){
		return CommentModel::validate(self::$validators['entity']);
	}
	static function create(){
		if(self::validate()){
			return CommentModel::create('entity_comment');
		}
	}
}
