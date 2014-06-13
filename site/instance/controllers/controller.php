<?
//Start database connetion
global $defaultConnectionInfo;
$db = Db::initialize($defaultConnectionInfo);

$cache = Cache::initialize(
	array('servers'=>array(
		array('localhost',11211)),
		
	));

if(Config::$x['inScript']){
	//next controller (scripts don't need the stuff below)
	return true;
}


//+	bot prevention handling {
if(!User::isAdmin()){
	if($_GET['_clearBot']){
		Cache::delete('bot-detected-'.$_SERVER['REMOTE_ADDR']);
	}
	
	Bot::disallow();

	//limit page loads 
	//this is a full page (or ajax request, so limit frequence)
	Bot::indicator('pageload',30,60*2);

	//limit update, create, and form posts
	if(Page::$in['_cmd_create'] || Page::$in['_cmd_update'] || $_POST){	
		Bot::indicator('form',10,60*5);
	}

	Bot::updateList();
	Bot::disallow();
}
//+	}


//Start session
Session::$start = false;
Session::start();


//get the "data" cookie used for general, single page (non continuous) data saves
Cookie::getData();

function error($message){
	Page::error($message);
	Display::show('standardPage');
	exit;
}

function getId($type=null){
	$id = CRUDController::getId();
	if(!$id){
		error('No id provided');
	}
	Display::$json['id'] = $id;
	if(!$type){
		$type = Arrays::at(RequestHandler::$urlTokens,-2);
		if(strtolower($type) == 'read'){
			$type = Arrays::at(RequestHandler::$urlTokens,-3);
		}
		$type = strtolower($type);
	}
	Display::$json['id_type'] = $type;
	Page::$data->id = $id;
	PageTool::$id = $id;
	return $id;
}
function badId(){
	unset(Display::$json['id'],Display::$json['id_type']);
	unset(Page::$data->id);
	PageTool::$id = null;
	error('Id not found');
}