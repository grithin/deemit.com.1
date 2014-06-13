<?
class PageTool extends RelationModel{
	static function read(){
		Page::$data->relater = Db::row('select title from entity where id = '.abs(Page::$in['relater']));
		Page::$data->relatee = Db::row('select title from entity where id = '.abs(Page::$in['relatee']));
		Page::notice('Please make sure you are not posting a duplicate relation and that you are providing sufficient detail');
		Page::warning('If your creation is deleted by the community, your account may be auto-deleted');
		parent::read();
	}
	static function validate(){
		Page::filterAndValidate(array(
			'reason_title' => IVE::$in['title'],
			'for_factor' => self::$validators['for'],
			'control_factor' => self::$validators['control'],
			'reason_text' => IVE::$in['text'],
			'relater' => self::$validators['entity'],
			'relatee' => self::$validators['entity'],
		));
		return !Page::errors();
	}
	static function create(){
		if(self::validate()){
			$table = 'entity_relation';
			$insert = Arrays::extract(array('reason_title','for_factor','control_factor','reason_text'),Page::$in);
			$insert['time_created'] = i()->Time()->datetime();
			$insert['significance'] = UserSignificance::get();
			$insert['user_id'] = User::id();
			$insert['_id_relater'] = Page::$in['relater'];
			$insert['_id_relatee'] = Page::$in['relatee'];
			$id = Db::insert($table,$insert);
			
			//add vote by user for post
			$voteInsert = Arrays::extract(array('significance','time_created','user_id'),$insert);
			$voteInsert['_id'] = $id;
			Db::insert($table.'_vote',$voteInsert);
			
			return $id;
		}
	}
}
