<?
class Lock{
	static $locks;
	static function on($name,$timeout=0){
		$file = Config::$x['storageFolder'].'lock-'.$name;
		$fh = fopen($file,'w');
		
		while(self::$locks[$name] || !flock($fh,LOCK_EX|LOCK_NB)){
			if(!$timeout || time() - $start >= $timeout){
				return false;
			}
			usleep(200000);
		}
		self::$locks[$name] = $fh;
		return true;
	}
	static function isOn($name){
		if(self::on($name)){
			self::off($name);
			return false;
		}
		return true;
	}
	static function off($name){
		$file = Config::$x['storageFolder'].'lock-'.$name;
		flock(self::$locks[$name], LOCK_UN);
		unlink($file);
		unset(self::$locks[$name]);
	}
}
