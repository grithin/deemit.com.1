<?
class EntityModel{
	static $validators = array(
			'keywords' => array('f:toRegex|@[^a-z,_\-0-9 \']@i','f:trim','!v:filled','p:checkKeywords'),
			'type' => '!v:isInteger,!v:intInRange|0,!p:checkEntityType',
			'entity' => '!v:isInteger,!p:checkEntity'
		);
	static function checkEntityType($value){
		if(!Db::check('entity_type',$value)){
			InputException::throwError('Entity type id does not match existing entity type');
		}
	}
	static function checkEntity($value){
		if(!Db::check('entity',$value)){
			InputException::throwError('{_FIELD_} is not an entity');
		}
	}
	
	static $tags;
	static function checkKeywords($value){
		self::$tags = Tag::parse($value);
		if(!self::$tags){
			InputException::throwError('Must provide keywords');
		}
	}
	
}
