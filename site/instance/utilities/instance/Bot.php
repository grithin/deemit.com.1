<?
class Bot{
	static $detected = false;
	static function detected(){
		if(self::$detected || Cache::get('bot-detected-'.$_SERVER['REMOTE_ADDR'])){
			return true;
		}
	}
	static function disallow(){
		if(self::detected()){
			die('Bot Behavior Detected.  Please slow down.  You will need to wait a while before accessing the site again');
		}
	}
	static function updateList(){
		if(self::detected()){
			Db::insert('bot_ip_detection',array(
					'ip' => $_SERVER['REMOTE_ADDR'],
					'time_created' => i()->Time()->datetime(),
				));
			$botDetections = Db::row('bot_ip_detection',array('time_created?>='=>i()->Time('-1 week')->datetime(),'ip'=>$_SERVER['REMOTE_ADDR']),'count(*)');
			if($botDetections > 3){
				Cache::set('bot-detected-'.$_SERVER['REMOTE_ADDR'],1,60*60*24);
			}
		}
	}
	static function indicator($key,$limit,$expiry){
		//allow googlebot
		if(!User::id() && preg_match('@googlebot|MSNBot@i',$_SERVER['HTTP_USER_AGENT'])){
			return;
		}
		//allow admin users
		$key = 'bot-limit-'.$key.'-'.$_SERVER['REMOTE_ADDR'];
		$count = Cache::get($key);
		if(!$count){
			Cache::set($key,1,$expiry);
		}else{
			if($count >= $limit){
				Cache::set('bot-detected-'.$_SERVER['REMOTE_ADDR'],1,60*60);
				self::$detected = true;
			}
			Cache::increment($key);
		}
	}
}
