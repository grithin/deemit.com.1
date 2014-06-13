<?
User::required();

//figure out the table based on referrer
$baseReferrerUrl = array_shift(explode('?',$_SERVER['HTTP_REFERER']));
$referrerParts = explode('/',$baseReferrerUrl);
$table = Arrays::at($referrerParts,-3);
$tables = array(
		'relation' => 'entity_relation',
		'entity' => 'entity',
		'user' => 'user'
	);
PageTool::$table = $tables[$table];
if(!PageTool::$table){
	die('Table not found');
}

CRUDController::handle();

Page::saveMessages();