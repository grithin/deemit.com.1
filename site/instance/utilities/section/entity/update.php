<?
class PageTool extends EntityModel{
	static $id;
	static function read(){
		$entity = Page::$data->entity = Db::row('select e.*, et.name type_name, u.display_name user_name
			from entity e 
				left join entity_type et on e.type_id = et.id
				left join user u on e.user_id = u.id
			where e.id = '.self::$id);
		
		if($entity){
			if($entity['user_id'] != User::id()){
				Page::error('You are not the owner and can not edit');
			}
			return true;
		}
		return false;
	}
	
	static function validate(){
		Page::filterAndValidate(array(
			'keywords' => self::$validators['keywords'],
			'description' => IVE::$in['text'],
		));
		return !Page::errors();
	}
	static function update(){
		if(self::validate()){
			$update = Arrays::extract(array('keywords','description'),Page::$in);
			Db::update('entity',$update,self::$id);
			Tag::associate('entity_tag',self::$id,self::$tags);
			return true;
		}
	}
}
