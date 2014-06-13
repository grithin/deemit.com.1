<?
class PageTool extends EntityModel{
	static function read(){
		Page::$data->entity_types = Db::columnKey('id','select id, name from entity_type order by name');
		Page::notice('Please make sure you are not posting a duplicate entity and that you are providing sufficient detail');
		Page::warning('If your creation is deleted by the community, your account may be auto-deleted');
	}
	
	static function validate(){
		Page::filterAndValidate(array(
			'title' => IVE::$in['title'],
			'keywords' => self::$validators['keywords'],
			'type_id' => self::$validators['type'],
			'description' => IVE::$in['text'],
		));
		return !Page::errors();
	}
	static function create(){
		if(self::validate()){
			$table = 'entity';
			$insert = Arrays::extract(array('title','keywords','type_id','description'),Page::$in);
			$insert['time_created'] = i()->Time()->datetime();
			$insert['significance'] = UserSignificance::get();
			$insert['user_id'] = User::id();
			$id = Db::insert($table,$insert);
			Tag::associate('entity_tag',$id,self::$tags);
			
			//add vote by user for post
			$voteInsert = Arrays::extract(array('significance','time_created','user_id'),$insert);
			$voteInsert['_id'] = $id;
			Db::insert($table.'_vote',$voteInsert);
			
			return $id;
		}
	}
}
