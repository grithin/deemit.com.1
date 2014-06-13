<?
///General tools for use anywhere
class Tool{
	///get the size of a directory
	/**
	@param	dir	path to a directory
	*/
	static function dirSize($dir){//directory size
		if(is_array($subs=scandir($dir))){
			$size = 0;
			$subs=array_slice($subs,2,count($subs)-2);
			if($sub_count=count($subs)){
				for($i=0;$i<$sub_count;$i++){
					$temp_sub=$dir.'/'.$subs[$i];
					if(is_dir($temp_sub)){
						$size+=Tool::dirSize($temp_sub);
					}else{
						$size+=filesize($temp_sub);
					}
				}
			}
			return $size;
		}
	}
	static $regexExpandCache = array();
	///expand a regex pattern to a list of characters it matches
	static function regexExpand($regex){
		$ascii = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~'."\t\n";
		if(!self::$regexExpandCache[$regex]){
			preg_match_all($regex,$ascii,$matches);
			self::$regexExpandCache[$regex] = implode('',$matches[0]);
		}
		return self::$regexExpandCache[$regex];
	}
	///generate a random string
	/**
	@note this function is overloaded and can take either two or three params.
	@param	1	length or min length
	@param	2	length range max or regex pattern (with delimeter)
	@param	3	regex pattern (with delimeter): Ex "#[a-z]#i"
	@return	random string matching the regex pattern
	*/
	static function randomString(){
		$args = func_get_args();
		if(func_num_args() >= 3){
			$length = rand($args[0],$args[1]);
			$match = $args[2];
		}else{
			$length = $args[0];
			//In case this is 3 arg overloaded with $match null for default
			if(!is_int($args[1])){
				$match = $args[1];
			}
		}
		if(!$match){
			$match = '@[a-z0-9]@i';
		}
		$allowedChars = self::regexExpand($match);
		$range = strlen($allowedChars) - 1;
		for($i=0;$i<$length;$i++){
			$string .= $allowedChars[mt_rand(0,$range)];
		}
		return $string;
	}
	
	///used for time based synchronization
	static function then($name=1){
		if(self::$then[$name]){
			return self::$then[$name];
		}
		return self::$then[$name] = time();
	}
	///turns a camelCased string into an underscore separated string
	/**
	@param	string	string to morph
	@return	underscope separated string
	*/
	static function camelToUnderscore($string){
		return preg_replace('@[A-Z]@e','strtolower("_$0")',$string);
	}
	
	///pluralized a word.  Limited abilities.
	/**
	@param	word	word to pluralize
	@return	pluralized form of the word
	*/
	static function pluralize($word){
		if(substr($word,-1) == 'y'){
			return substr($word,0,-1).'ies';
		}
		if(substr($word,-1) == 'h'){
			return $word.'es';
		}
		return $word.'s';
	}
	///capitalize first letter in certain words
	/**
	@param	string	string to capitalize
	@return	a string various words capitalized and some not
	*/
	static function capitalize($string){
		$exclude = array('to', 'the', 'in', 'at', 'for', 'or', 'and', 'so', 'with', 'if', 'a', 'an', 'of', 
			'to', 'on', 'with', 'by', 'from', 'nor', 'not', 'after', 'when', 'while');
		$fullCap = array('cc');
		$words = preg_split('@[\t ]+@',$string);
		foreach($words as &$v){
			if(in_array($v,$fullCap)){
				$v = strtoupper($v);
			}elseif(!in_array($v,$exclude)){
				$v = ucfirst($v);
			}
		}unset($v);
		return implode(' ',$words);
	}
	///turns a string into a camel cased string
	/**
	@param	string	string to camelCase
	*/
	static function toCamel($string){
		preg_match('@[ _]*[^ _]*@',$string,$match);
		$firstWord = strtolower($match[0]);
		$cString = $firstWord;
		preg_match_all('@[ _]+([^ _]+)@',$string,$match);
		if($match[1]){
			foreach($match[1] as $word){
				$cString .= ucfirst($word);
			}
		}
		return $cString;
	}
	///determines if string is a float
	static function isFloat($x){
		if((string)(float)$x == $x){
			return true;
		}
	}
	///determines if a string is an integer
	static function isInt($x){
		if(is_int($var)){
			return true;
		}
		if((string)(int)$x == $x && $x !== true & $x !== false && $x !== null){
			return true;
		}
	}
	///escapes the delimiter and delimits the regular expression.
	/**If you already have an expression which has been preg_quoted in all necessary parts but without concern for the delimiter
	@string	string to delimit
	@delimiter	delimiter to use.  Don't use a delimiter quoted by preg_quote
	*/
	static function pregDelimit($string,$delimiter='@'){
		return $delimiter.preg_replace('/\\'.$delimiter.'/', '\\\\\0', $string).$delimiter;
	}
	///checks if there is a regular expression error in a string
	/**
	@regex	regular expression including delimiters
	@return	false if no error, else string error
	*/
	static $regexError;
	static function regexError($regex){
		$currentErrorReporting = error_reporting();
		error_reporting($current & ~E_WARNING);
		
		set_error_handler(array('self','captureRegexError'));
	
		preg_match($regex,'test');
		
		error_reporting($currentErrorReporting);
		restore_error_handler();
		
		if(self::$regexError){
			$return = self::$regexError;
			self::$regexError == null;
			return $return;
		}
	}
	///temporary error catcher used with regexError
	static function captureRegexError($code,$string){
		self::$regexError = $string;
	}
	///quote a preg replace string
	static function pregQuoteReplaceString($str) {
		return preg_replace('/(\$|\\\\)(?=\d)/', '\\\\\1', $str);
	}
	///translate human readable size into bytes
	static function byteSize($string){
		preg_match('@(^|\s)([0-9]+)\s*([a-z]{1,2})@i',$string,$match);
		$number = $match[2];
		$type = strtolower($match[3]);
		switch($type){
			case 'k':
			case 'kb':
				return $number * 1024;
			break;
			case 'mb':
			case 'm':
				return $number * 1048576;
			break;
			case 'gb':
			case 'g':
				return $number * 1073741824;
			break;
			case 'tb':
			case 't':
				return $number * 1099511627776;
			break;
			case 'pb':
			case 'p':
				return $number * 1125899906842624;
			break;
		}
	}
	///like the normal implode but removes empty values
	static function explode($separator,$string){
		$array = explode($separator,$string);
		Arrays::remove($array);
		return array_values($array);
	}
}