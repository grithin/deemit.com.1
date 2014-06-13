<?
class PageTool extends RelationModel{
	static $id;
	static function read(){
		$tags = Tag::parse(Page::$in['q']);
		if($tags){
			foreach($tags as $tag){
				//weight by how large title is compared to tag
				$tagLength = strlen($tag) * 100;
				$queries[] = 'select id, '.$tagLength.'/char_length(title) weight  from entity where title like '.Db::quote('%'.$tag.'%');
			}
			$tagIds = Tag::ids($tags);
			foreach($tagIds as $tagId){
				$queries[] = 'select _id id, 3 weight from entity_tag where tag_id = '.$tagId;
			}
			
			$sql = 'select 
						e.id, e.title, e.type_id, e.user_id, e.keywords, e.significance,
						u.display_name user_name, u.significance user_significance,
						et.name type_name,
						t.weight
					from entity e
						left join user u on e.user_id = u.id
						left join entity_type et on et.id = e.type_id
						inner join
						(	select id, sum(weight) weight
							from
							(
								'.implode("\n	UNION ALL	\n",$queries).'
							) t
							group by id
						) t on e.id = t.id
					order by t.weight desc, e.title';
			
			Page::$data->results = SortPage::page($sql,50,500);
		}
	}
	
}
