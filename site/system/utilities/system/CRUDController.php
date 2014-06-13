<?
///Create Read Update Delete general class

/**
Static b/c there appears little reason to have more than one on a single page
*/
class CRUDController{
	static function getId(){
		$id = abs(Page::$in['id']);
		if($id){
			return $id;
		}
		
		$tokens = RequestHandler::$urlTokens;
		krsort($tokens);
		foreach($tokens as $token){
			$id = abs($token);
			if($id){
				return $id;
			}
		}
		
	}
	static $attempted;
	static $called;
	//static $specialHandler = 'PageTool';///<The class, instance or name, to be used for special handling of actions
	static function handle($commands=array('create','update','delete','read'),$default='read'){
		self::$attempted = self::$called = array();
		foreach($commands as $command){
			if(Page::$in['_cmd_'.$command]){
				$return = self::callFunction($command);
				if($return === null || $return === false){
					continue;
				}
				return new CRUDResult($command,$return,Page::$in['_cmd_'.$command]);
			}
		}
		if($default && !in_array($default,self::$attempted)){
			$return = self::callFunction($default,Page::$in['_cmd'][$default]);
			return new CRUDResult($default,$return);
		}
		return new CRUDResult('',null);
	}
	static function getFunction($command,$subcommand=null){
		if(!$subcommand){
			$subcommand = Page::$in['_cmd_'.$command];
		}
		if(method_exists('PageTool',$command.'_'.$subcommand)){
			return array('PageTool',$command.'_'.$subcommand);
		}elseif(method_exists('PageTool',$command)){
			return array('PageTool',$command);
		}
		return false;
	}
	//callbacks applied at base for antibot behavior
	static function callFunction($command,$subcommand=null,$error=false){
		self::$attempted[] = $command;
		$function = self::getFunction($command);
		if($function){
			self::$called[] = $command;
			$return = call_user_func($function);
			return $return;
		}
		if($error){
			Page::error('Unsupported command');
		}
	}
	static function resultHandler($result){
		switch($result->type){
			case 'create':
				if($result->return == true){
					Page::success('Creation Successful');
				}else{
					Page::error('Creation Failed');
				}
				
				//get specific read operation
				self::callFunction('read',Page::$in['_cmd_'.$result->type]);
			break;
			case 'update':
				if($result->return == true){
					Page::success('Update Successful');
				}else{
					Page::error('Update Failed');
				}
				
				//get specific read operation
				self::callFunction('read',Page::$in['_cmd_'.$result->type]);
			break;
			case 'delete':
				if(Page::$in['_ajax']){
					Display::$json['return'] = $result->return;
					Display::ajaxOut(json_encode(Display::$json),'json');
					exit;
				}else{
					
					if($result->return == true){
						Page::success('Update Successful');
					}else{
						Page::error('Update Failed');
					}
					
					//get specific read operation
					self::callFunction('read',Page::$in['_cmd_'.$result->type]);
				}
			break;
		}
	}
}

class CRUDResult{
	function __construct($type,$return,$subType=null){
		$this->type = $type;
		$this->return = $return;
		$this->subType = $subType;
		$this->attempted = CRUDController::$attempted;
		$this->called = CRUDController::$called;
		if($type){
			$this->$type = $return;
		}
	}
}