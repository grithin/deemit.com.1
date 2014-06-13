<?


class Cache{
	/// reference to primary PDO instance
	public $cacher;	
	/// latest result set returning from $db->query()
	public $result;				// latest result set returns from $db->query()
	/// Name of the primary database connection
	static $primary = 0;
	/// named Db class instances
	static $connections = array();
	
	///prevent public instantiation
	private function __construct(){}
	
	///make connection
	/**
	@param	connection	name of the connection
	*/
	function connect(){
		$this->cacher = new Memcached;
		foreach($this->connectionInfo['servers'] as $v){
			if(!$this->cacher->addserver($v[0],$v[1],$v[2])){
				Debug::quit('Failed to add cacher',$v);
			}
		}
		$this->cacher->set('on',1);
		if(!$this->cacher->get('on')){
			Debug::quit('Failed to get cache');
		}
	}
	///lazy load a new db instance; uses singleton base on name.
	/**
	@param	connectionInfo	array:
		@verbatim
	array(
		servers => [ip/name,port,weight]
	*/
	static function initialize($connectionInfo,$name=0){
		if(!isset(self::$connections[$name])){
			//set primary if no connections except this one
			if(!self::$connections){
				self::$primary = $name;
			}
			//add connection
			$class = __class__;
			self::$connections[$name] = new $class();
			self::$connections[$name]->connectionInfo = $connectionInfo;
		}
		return self::$connections[$name];
	}
	/// used to translate static calls to the primary database instance
	static function __callStatic($name,$arguments){
		$that = self::$connections[self::$primary];
		if(!$that->cacher){
			$that->connect();
		}
		if(method_exists($that,$name)){
			return call_user_func_array(array($that,$name),$arguments);
		}
		return call_user_func_array(array($that->cacher,$name),$arguments);
	}
	function __call($name,$arguments){
		if(!$this->cacher){
			$this->connect();
		}
		if(method_exists($this,$name)){
			return call_user_func_array(array($this,$name),$arguments);
		}
		return call_user_func_array(array($this->cacher,$name),$arguments);
	}
	///updatable set with timeout handled specially
	/**
	@param	name	name of cache key
	@param	value	value of cache
	@param	updateTime	time after which cache should be updated
	@param	relativeExpiry	time cache should actually expire after update time
	*/
	private function uset($name,$value,$updateTime,$relativeExpiry='60 seconds'){
		$updateTime = i()->Time($updateTime);
		$updateTimeUnix = $updateTime->unix();
		if($relativeExpiry){
			$expiryTimeUnix = $updateTime->relative('+'.$relativeExpiry)->unix();
			$expiry = $expiryTimeUnix - $updateTimeUnix;
		}
		$this->cacher->set($name,$value,$expiry);
		$this->cacher->set($name.'-update',$updateTimeUnix,$expiry);
	}
	///updatable get with timeout handled specially
	/**
	@param	name	name of cache key
	@param	expiry	expiry to use on updating cache indicator (to prevent a bad update from preventing updates completely)
	*/
	private function uget($name,$expiry=60){
		$update = $this->cacher->get($name.'-update',null,$casToken);
		//indates update needed
		if(Tool::isInt($update) && time() > $update){
			Debug::quit($update);
			if($this->cacher->cas($casToken,$name.'-update','updating',$expiry)){
				return false;
			}
		}
		//no update time found and not updating
		elseif($update === false){
			return false;
		}
		return $this->cacher->get($name,$value,$expiry);
	}
}
