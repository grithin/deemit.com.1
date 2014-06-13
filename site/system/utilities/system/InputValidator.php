<?
class InputValidator{
	static $errorMessages = array(
//+	basic validators{
			'exists' => 'Missing field {_FIELD_}',
			'filled' => 'Missing field {_FIELD_}',
			'existsInTable' => 'No reord of {_FIELD_} found',
			'isInteger' => '{_FIELD_} must be an integer',
			'isFloat' => '{_FIELD_} must be a decimal',
			'matchRegex' => '{_FIELD_} must match %s',
			'existsAsKey' => '{_FIELD_} did not contain an accepted value',
			'existsAsValue' => '{_FIELD_} did not contain an accepted value',
			'isEmail' => '{_FIELD_} must be a valid email',
			'isUrl' => '{_FIELD_} must be a URL',
			'intInRange.max' => '{_FIELD_} must be %s or less',
			'intInRange.min' => '{_FIELD_} must be %s or more',
			'length' => '{_FIELD_} must be a of a length equal to %s',
			'lengthRange.max' => '{_FIELD_} must have a length of %s or less',
			'lengthRange.min' => '{_FIELD_} must have a length of %s or more',
			'date' => '{_FIELD_} must be a date.  Most date fromats are accepted',
//+	}
//+	More specialized validators{			
			'phone.area' => 'Please include an area code in {_FIELD_}',
			'phone.check' => 'Please check {_FIELD_}',
			'zip' => '{_FIELD_} was malformed',
			'age.max' => '{_FIELD_} too old.  Must be at most %s',
			'age.min' => '{_FIELD_} too recent.  Must be at least %s',
//+	}
		);
	
//+	basic validators{
	static function exists(&$value,$pass=false){
		if(!isset($value)){
			if(!$pass){
				InputException::throwError(self::$errorMessages['exists']);
			}else{
				InputException::throwError(self::$errorMessages['exists'],InputException::breakPass);
			}
			
		}
	}
	static function filled(&$value,$pass=false){
		if(!isset($value) || $value === ''){
			if(!$pass){
				InputException::throwError(self::$errorMessages['filled']);
			}else{
				InputException::throwError(self::$errorMessages['filled'],InputException::breakPass);
			}
			
		}
	}
	static function existsInTable(&$value,$table,$field='id'){
		if(!Db::check($table,array($field=>$value))){
			InputException::throwError(self::$errorMessages['existsInTable']);
		}
	}
	static function isInteger(&$value){
		if(!Tool::isInt($value)){
			InputException::throwError(self::$errorMessages['isInteger']);
		}
	}
	static function isFloat(&$value){
		if(filter_var($value, FILTER_VALIDATE_FLOAT) === false){
			InputException::throwError(self::$errorMessages['isFloat']);
		}
	}
	static function matchRegex(&$value,$regex){
		if(!preg_match($regex,$value)){
			$chars = Tool::regexExpand($regex);
			InputException::throwError(sprintf(self::$errorMessages['matchRegex'],'"'.$chars.'"'));
		}
	}
	static function existsAsKey(&$value,$array){
		if(!isset($array[$value])){
			InputException::throwError(self::$errorMessages['existsAsKey']);
		}
	}
	static function existsAsValue(&$value){
		$args = func_get_args();
		array_shift($args);
		if(is_array($args[0])){
			$array = $args[0];
		}else{
			$array = $args;
		}
		if(!in_array($value,$array)){
			InputException::throwError(self::$errorMessages['existsAsValue']);
		}
	}
	static function isEmail($value){
		if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
			InputException::throwError(self::$errorMessages['isEmail']);
		}
	}
	static function isUrl($value){
		if(!filter_var($value, FILTER_VALIDATE_URL)){
			InputException::throwError(self::$errorMessages['isUrl']);
		}
	}
	static function intInRange($value,$min=null,$max=null){
		if(Tool::isInt($max) && $value > $max){
			InputException::throwError(sprintf(self::$errorMessages['inRange.max'],$max));
		}
		if(Tool::isInt($min) && $value < $min){
			InputException::throwError(sprintf(self::$errorMessages['inRange.min'],$min));
		}
	}
	static function length($value,$length){
		$actualLength = strlen($value);
		if($actualLength != $length){
			InputException::throwError(sprintf(self::$errorMessages['length'],$length));
		}
	}
	static function lengthRange($value,$min=null,$max=null){
		$actualLength = strlen($value);
		if(Tool::isInt($max) && $actualLength > $max){
			InputException::throwError(sprintf(self::$errorMessages['lengthRange.max'],$max));
		}
		if(Tool::isInt($min) && $actualLength < $min){
			InputException::throwError(sprintf(self::$errorMessages['lengthRange.min'],$min));
		}
	}
	static function date(){
		try{
			i()->Time($value);
		}catch(Exception $e){
			InputException::throwError(self::$errorMessages['time']);
		}
	}
//+	}
//+	specialized validators{
	static function zip($value){
		if (!preg_match("/^([0-9]{5})(-[0-9]{4})?$/i",$value)) {
			InputException::throwError(self::$errorMessages['zip']);
		}
	}
	static function phone(&$value){
		if(strlen($value) == 11 && substr($value,0,1) == 1){
			$value = substr($value,1);
		}
		if(strlen($value) == 7){
			InputException::throwError(self::$errorMessages['phone.area']);
		}
		
		if(strlen($value) != 10){
			InputException::throwError(self::$errorMessages['phone.check']);
		}
	}
	
	static function age($value,$min=null,$max=null){
		$time = i()->Time($value);
		$age = $time->diff(i()->Time('now'));
		if(Tool::isInt($max) && $age->y > $max){
			InputException::throwError(sprintf(self::$errorMessages['age.max'],$max));
		}
		if(Tool::isInt($min) && $age->y < $min){
			InputException::throwError(sprintf(self::$errorMessages['age.min'],$min));
		}
	}
}
//+	}

class InputException extends Exception{
	const breakError = 1;///<to ensure no more validators applied to this value after this error
	const breakPass = 2;///<to ensure no more validators applied to this value even though this was no an error
	static function throwError($error,$code=0){
		throw new InputException($error,$code);
	}
}