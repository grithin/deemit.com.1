<?
class PageTool{
	static function validate(){
		Page::filterAndValidate(array(
			'first_name' => 'name',
			'last_name' => 'name',
			'time_born' => 'userBirthdate',
			'email' => 'email,!p:emailCheck',
			'password' => 'password',
			'display_name' => 'name,!p:displayNameCheck',
			'agree' => '!v:filled',
			'' => '!p:ipCheck',
		));
		return !Page::errors();
	}
	
	static function create(){
		if(self::validate()){
			$insert = Arrays::extract(array('first_name','last_name','time_born','email','password','display_name','referrer'),Page::$in);
			$insert['time_born'] = i()->Time($insert['time_born'])->datetime();
			$insert['time_created'] = i()->Time()->datetime();
			$insert['significance'] = 0;
			$insert['password'] = sha1($insert['password']);
			$id = Db::insert('user',$insert);
			
			//add to email verfiication table
			$verificationCode = Tool::randomString(20,'#[a-z0-9]#');
			Db::insert('user_email_verification',array(
					'time_created' => i()->Time()->datetime(),
					'user_id' => $id,
					'email' => $insert['email'],
					'code' => $verificationCode,
				));
			
			//add ip to user ip table
			Db::insert('user_ip',array(
					'time_created' => i()->Time()->datetime(),
					'user_id' => $id,
					'ip' => $_SERVER['REMOTE_ADDR'],
				));
			
			//update significance
			UserSignificance::update(10,'user creation: '.$id,$id);
			
			Page::$data->name = $insert['first_name'];
			Page::$data->code = $verificationCode;
			Page::$data->userId = $id;
			
			Email::send(Display::getTemplate('user/signup.email'),$insert['email'],'Signup Successful','confirm@deemit.com');
			return true;
		}
	}
	static function ipCheck(){
		$ipWithinDay = Db::check('user_ip',
			array('ip' => $_SERVER['REMOTE_ADDR'],'time_created?>' => i()->Time('-1 day')->datetime()));
		if($ipWithinDay){
			InputException::throwError('You have already submitted a form in the last 24 hours.  Please wait before submitting another.');
		}
	}
	static function emailCheck($value){
		if(Db::check('user',array('email' => $value))){
			InputException::throwError('There is already a user with the email address you entered');
		}
	}
	static function displayNameCheck($value){
		if(Db::check('user',array('display_name' => $value))){
			InputException::throwError('There is already a user with the display name you entered');
		}
	}
}
