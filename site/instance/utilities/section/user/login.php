<?
class pageTool{
	static $user;
	
	static function validate(){
		Page::filterAndValidate(array(
			'email' => 'email',
			'password' => 'password',
			'' => '!p:loginCheck'
		));
		return !Page::errors();
	}
	
	static function update(){
		if(self::validate()){
			User::login(self::$user['id'],self::$user['display_name']);
			if(User::hasPrivilege('general admin')){
				$_SESSION['isAdmin'] = true;
			}
			return true;
		}
	}
	static function loginCheck(){
		if(Page::errors()){
			return;
		}
		$user = Db::row('user',array(
					'email' => Page::$in['email'],
					'password' => sha1(Page::$in['password'])
				),
			'id,is_disabled,is_verified,display_name');
		if(!$user){
			InputException::throwError('Email - Password combo not found');
		}elseif($user['is_disabled']){
			InputException::throwError('User account is disabled');
		}elseif(!$user['is_verified']){
			InputException::throwError('User account email needs verification');
		}
		self::$user = $user;
	}
}
