<?
class pageTool{
	static $baseEntitySql = 'select e.id, e.title, e.significance, e.keywords,
			et.name type_name, 
			u.display_name user_name, u.significance user_significance, u.id user_id
		from entity e 
			left join entity_type et on e.type_id = et.id
			left join user u on e.user_id = u.id
			';
	static $baseEntityRelationSql = 'select er.id, er.reason_title title, er.significance, er.for_factor, er.control_factor,
			r1.title relater_title, r1.id relater_id, 
			r2.title relatee_title, r2.id relatee_id, 
			u.display_name user_name, u.significance user_significance, u.id user_id
		from entity_relation er 
			left join entity r1 on er._id_relater = r1.id
			left join entity r2 on er._id_relatee = r2.id
			left join user u on er.user_id = u.id
			';
	static function makeEntitySql($sql,$order){
			return self::$baseEntitySql.' inner join ('.$sql.') t on t._id = e.id
				order by '.$order.'
				limit 10';
	}
	static function makeEntityRelationSql($sql,$order){
			return self::$baseEntityRelationSql.' inner join ('.$sql.') t on t._id = er.id
				order by '.$order.'
				limit 10';
	}
	static function read(){
		
		Page::$data->order = array(
				'Most Recent',
				'Most Commented (daily)',
				'Most Voted (daily)',
				'Most Commented (weekly)',
				'Most Voted (weekly)',
				'Most Commented (month)',
				'Most Voted (month)'
			);
		$day = Db::quote(i()->Time('-1 day')->datetime());
		$week = Db::quote(i()->Time('-1 week')->datetime());
		$month = Db::quote(i()->Time('-1 month')->datetime());
		//10 minute offset to allow user to delete if he/she changes mind
		$recent = Db::quote(i()->Time('-10 minutes')->datetime());
		
		Page::$data->highlights = Cache::uget('frontpage-highlights');
		if(Page::$data->highlights ===  false){
			Page::$data->highlights['entity']['Most Recent'] = Db::rows(self::$baseEntitySql.' where e.time_created < '.$recent.' order by e.time_created desc limit 10');
			Page::$data->highlights['entity']['Most Commented (daily)'] = Db::rows(self::makeEntitySql('select _id, count(*) count 
					from entity_comment
					where time_created >='.$day.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity']['Most Commented (weekly)'] = Db::rows(self::makeEntitySql('select _id, count(*) count 
					from entity_comment
					where time_created >='.$week.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity']['Most Commented (month)'] = Db::rows(self::makeEntitySql('select _id, count(*) count 
					from entity_comment
					where time_created >='.$month.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity']['Most Voted (daily)'] = Db::rows(self::makeEntitySql('select _id, count(*) count 
					from entity_vote
					where time_created >='.$day.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity']['Most Voted (weekly)'] = Db::rows(self::makeEntitySql('select _id, count(*) count 
					from entity_vote
					where time_created >='.$week.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity']['Most Voted (month)'] = Db::rows(self::makeEntitySql('select _id, count(*) count 
					from entity_vote
					where time_created >='.$month.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			
			Page::$data->highlights['entity_relation']['Most Recent'] = Db::rows(self::$baseEntityRelationSql.' where er.time_created < '.$recent.' order by er.time_created desc limit 10');
			Page::$data->highlights['entity_relation']['Most Commented (daily)'] = Db::rows(self::makeEntityRelationSql('select _id, count(*) count 
					from entity_relation_comment
					where time_created >='.$day.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity_relation']['Most Commented (weekly)'] = Db::rows(self::makeEntityRelationSql('select _id, count(*) count 
					from entity_relation_comment
					where time_created >='.$week.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity_relation']['Most Commented (month)'] = Db::rows(self::makeEntityRelationSql('select _id, count(*) count 
					from entity_relation_comment
					where time_created >='.$month.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity_relation']['Most Voted (daily)'] = Db::rows(self::makeEntityRelationSql('select _id, count(*) count 
					from entity_relation_vote
					where time_created >='.$day.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity_relation']['Most Voted (weekly)'] = Db::rows(self::makeEntityRelationSql('select _id, count(*) count 
					from entity_relation_vote
					where time_created >='.$week.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			Page::$data->highlights['entity_relation']['Most Voted (month)'] = Db::rows(self::makeEntityRelationSql('select _id, count(*) count 
					from entity_relation_vote
					where time_created >='.$month.'
					group by _id
					order by count desc 
					limit 10','t.count'));
			
			Cache::uset('frontpage-highlights',Page::$data->highlights,'+600 seconds');
		}
	}
}
