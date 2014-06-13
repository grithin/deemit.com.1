<?
class UserModel{
	static $validators = array(
			'user' => '!v:isInteger,!p:checkUser'
		);
	static function checkUser($value){
		if(!Db::check('user',$value)){
			InputException::throwError('{_FIELD_} is not a user');
		}
	}
}
