<?
class PageTool extends EntityModel{
	static $id;
	static function read(){
		$user = Page::$data->user = Db::row('user',self::$id);
		if($user){
			CommentModel::read('user',self::$id);
			
			Page::$data->relations = Db::rows('select 
					er.id, er.reason_title, er.time_created, er.for_factor, er.control_factor,
					e1.id relatee_id, e1.title relatee_title,
					e2.id relater_id, e2.title relater_title
				from entity_relation er
					left join entity e1 on er._id_relatee = e1.id
					left join entity e2 on er._id_relater = e2.id
				where er.user_id = '.$user['id']);
			
			Page::$data->entities = Db::rows('select id, title, time_created, significance
				from entity
				where user_id = '.$user['id']);
				
			return $user;
		}
	}
}
