<?
class RelationModel extends EntityModel{
	static $forFactors = array(
				'-10' => '-10 (100% against)',
				'-9' => '-9',
				'-8' => '-8',
				'-7' => '-7 (Largely against)',
				'-6' => '-6',
				'-5' => '-5',
				'-4' => '-4',
				'-3' => '-3 (Minorly against)',
				'-2' => '-2',
				'-1' => '-1',
				'0' => 'Not Considered',
				'1' => '1',
				'2' => '2',
				'3' => '3 (Minorly for)',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7 (Largely for)',
				'8' => '8',
				'9' => '9',
				'10' => '10 (100% for)',
			);
	static $controlFactors = array(
				'0' => 'Not Considered',
				'1' => '1',
				'2' => '2 (Minor influence)',
				'3' => '3 ',
				'4' => '4',
				'5' => '5 (Significant influence)',
				'6' => '6',
				'7' => '7 (Large Influence)',
				'8' => '8 ',
				'9' => '9',
				'10' => '10 (Complete control)',
			);
	static function read(){
		Page::$data->forFactors = self::$forFactors;
		Page::$data->controlFactors = self::$controlFactors;
	}
	static function init(){
		self::$validators['for'] = '!v:filled,v:intInRange|-10;10';
		self::$validators['control'] = '!v:filled,v:intInRange|0;10';
		self::$validators['relation'] = '!v:isInteger,!p:checkRelation';
	}
	static function checkRelation($value){
		if(!Db::check('entity_relation',$value)){
			InputException::throwError('{_FIELD_} is not an entity relation');
		}
	}
}
RelationModel::init();