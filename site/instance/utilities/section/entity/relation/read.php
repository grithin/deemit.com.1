<?
class PageTool extends RelationModel{
	static $id;
	static function read(){
		$relation = Page::$data->relation = Db::row('select r.*, u.display_name user_name
			from entity_relation r left join user u on r.user_id = u.id
			where r.id = '.self::$id);
		if($relation){
			CommentModel::read('entity_relation',self::$id);
			
			Page::$data->relater = Db::row('select id, title from entity where id = '.$relation['_id_relater']);
			Page::$data->relatee = Db::row('select id, title from entity where id = '.$relation['_id_relatee']);
		
			return Page::$data->relation;
		}
	}
}
