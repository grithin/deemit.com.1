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
			CommentModel::read('entity',self::$id);
			
			Page::$data->cRelatees = Db::rows('select e.id entity_id, e.title entity_title, erc.for_factor, erc.control_factor, erc.significance
				from entity_relation_compile erc left join entity e on erc._id_relatee = e.id
				where erc._id_relater = '.$entity['id'].'
				order by erc.significance desc, entity_title');
			Page::$data->cRelaters = Db::rows('select e.id entity_id, e.title entity_title, erc.for_factor, erc.control_factor, erc.significance
				from entity_relation_compile erc left join entity e on erc._id_relater = e.id
				where erc._id_relatee = '.$entity['id'].'
				order by erc.significance desc, entity_title');
			
			Page::$data->relatees = Db::rows('select e.id entity_id, e.title entity_title, er.id relation_id, er.reason_title, er.time_created, er.for_factor, er.control_factor, er.significance
				from entity e left join entity_relation er on e.id = er._id_relatee
				where er._id_relater = '.$entity['id'].'
				order by er.significance desc, entity_title');
			
			Page::$data->relaters = Db::rows('select e.id entity_id, e.title entity_title, er.id relation_id, er.reason_title, er.time_created, er.for_factor, er.control_factor, er.significance
				from entity e left join entity_relation er on e.id = er._id_relater
				where er._id_relatee = '.$entity['id'].'
				order by er.significance desc, entity_title');
				
			return $entity;
		}
	}
}
