<?
getId();
if(!CRUDController::handle()->return){
	badId();
}
if($_POST['relate']){
	User::required();
	$_SESSION['js']['relate'] = 'entity/relate.js';
	$_SESSION['css']['relate'] = 'entity/relate.css';
	$_SESSION['json']['relater'] = $page->id;
}
$page->title = 'Read Relation';
Display::end('@standardFullPage,,entity/relation/read');