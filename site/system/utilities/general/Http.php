<?
class Http{
	///parse a query string using a more standard less php specific rule (all repeated tokens turn into arrays, not just tokens with "[]")
	/**
	You can have this function include php field special syntax along with standard parsing.
	@param	string	string that matches form of a url query string
	@param	specialSyntax	whether to parse the string using php rules (where [] marks an array) in addition to "standard" rules
	*/
	function parseQuery($string,$specialSyntax = false){
		$parts = Tool::explode('&',$string);
		$array = array();
		foreach($parts as $part){
			list($key,$value) = explode('=',$part);
			$key = urldecode($key);
			$value = urldecode($value);
			if($specialSyntax && ($matches = self::getSpecialSyntaxKeys($key))){
				if(Arrays::isElement($matches,$array)){
					$currentValue = Arrays::getElement($matches,$array);
					if(is_array($currentValue)){
						$currentValue[] = $value;
					}else{
						$currentValue = array($currentValue,$value);
					}
					Arrays::updateElement($matches,$array,$currentValue);
				}else{
					Arrays::updateElement($matches,$array,$value);
				}
				unset($match,$matches);
			}else{
				if($array[$key]){
					if(is_array($array[$key])){
						$array[$key][] = $value;
					}else{
						$array[$key] = array($array[$key],$value);
					}
				}else{
					$array[$key] = $value;
				}
			}
		}
		return $array;
	}
	function buildQuery($array){
		$standard = array();
		foreach($array as $k=>$v){
			//exclude standard array handling from php array handling
			if(is_array($v) && !preg_match('@\[.*\]$@',$k)){
				$key = urlencode($k);
				foreach($v as $v2){
					$standard[] = $key.'='.urlencode($v2);
				}
				unset($array[$k]);
			}
		}
		$phpquery = http_build_query($array);
		$standard = implode('&',$standard);
		return Arrays::implode('&',array($phpquery,$standard));
	}
	///get all the keys invovled in a string that represents an array.  Ex: "bob[sue][joe]" yields array('bob','sue','joe')
	function getSpecialSyntaxKeys($string){
		if(preg_match('@^([^\[]+)((\[[^\]]*\])+)$@',$string,$match)){
			//match[1] = array name, match[2] = all keys
			
			//get names of all keys
			preg_match_all('@\[([^\]]*)\]@',$match[2],$matches);
			
			//add array name to beginning of keys list
			array_unshift($matches[1],$match[1]);
			
			return $matches[1];
		}
	}
	///appends multiple (key=>value)s to a url, replacing any key values that already exist
	/**
	@param	kvA	array of keys to values array(key1=>value1,key2=>value2)
	@param	url	url to be appended
	*/
	static function appendsUrl($kvA,$url=null,$replace=true){
		foreach($kvA as $k=>$v){
			if(is_array($v)){
				foreach($v as $subv){
					$url = self::appendUrl($k,$subv,$url,$replace);
				}
			}else{
				$url = self::appendUrl($k,$v,$url,$replace);
			}
		}
		return $url;
	}
	///appends name=value to query string, replacing them if they already exist
	/**
	@param	name	name of value
	@param	value	value of item
	@param	url	url to be appended
	*/
	static function appendUrl($name,$value,$url=null,$replace=true){
		if(!isset($url)){
			$url = $_SERVER['REQUEST_URI'];
		}
		$add = urlencode($name).'='.urlencode($value);
		if(preg_match('@\?@',$url)){
			$urlParts = explode('?',$url,2);
			if($replace){
				$urlParts[1] = preg_replace('@(^|&)'.preg_quote(urlencode($name)).'=(.*?)(&|$)@','$3',$urlParts[1]);
			}
			if($urlParts[1] != '&'){
				return $urlParts[0].'?'.$urlParts[1].'&'.$add;
			}
			return $urlParts[0].'?'.$add;
		}
		return $url.'?'.$add;
	}
	/**
	Removes key value pairs from url where key matches some regex.
	@param	regex	The regex to use for key matching.  If the regex does not contain the '@' for the regex delimiter, it is assumed the input is not a regex and instead just a string to be matched exactly against the key.  IE, '@bob@' will be considered regex while 'bob' will not
	*/
	static function removeFromQuery($regex,$url=null){
		if(!isset($url)){
			$url = urldecode($_SERVER['REQUEST_URI']);
		}
		if(!preg_match('@\@@',$regex)){
			$regex = '@^'.preg_quote($regex,'@').'$@';
		}
		$urlParts = explode('?',$url,2);
		if($urlParts[1]){
			$pairs = explode('&',$urlParts[1]);
			$newPairs = array();
			foreach($pairs as $pair){
				$pair = explode('=',$pair,2);
				#if not removed, include
				if(!preg_match($regex,urldecode($pair[0]))){
					$newPairs[] = $pair[0].'='.$pair[1];
				}
			}
			$url = $urlParts[0].'?'.implode('&',$newPairs);
		}
		return $url;
	}
	public function getAbsoluteUrl($url){
		$parts = explode('?',$url);
		preg_match('@(^.*?://.*?)(/.*$)@',$parts[0],$match);
		$pathParts = explode('/',$match[2]);
		$absParts = array();
		foreach($pathParts as $pathPart){
			if($pathPart == '..'){
				array_pop($absParts);
			}elseif($pathPart != '.'){
				$absParts[] = $pathPart;
			}
		}
		$path = implode('/',$absParts);
		$url = $match[1].$path;
		if($parts[1]){
			$url .= '?'.$parts[1];
		}
		return $url;
	}
	///relocate browser
	/**
	@param	location	location to relocate to
	@param	type	type of relocation; head for header relocation, js for javascript relocation
	*/
	static function redirect($location,$type='head'){
		if($type == 'head'){
			if(!$location){
				$location = $_SERVER['REQUEST_URI'];
			}
			header('Location: '.$location);
		}elseif($type=='js'){
			echo '<script type="text/javascript">';
			if(self::isInt($location)){
				if($location==0){
					$location = $_SERVER['REQUEST_URI'];
					echo 'window.location = '.$_SERVER['REQUEST_URI'].';';
				}else{
					echo 'javascript:history.go('.$location.');';
				}
			}else{
				echo 'document.location="'.$location.'";';
			}
			echo '</script>';
		}
		exit;
	}
}