<?
getId();
if(!CRUDController::handle()->return){
	badId();
}

if(Page::$in['relate']){
	User::required();
	$_SESSION['js']['relate'] = 'entity/relate.js';
	$_SESSION['css']['relate'] = 'entity/relate.css';
	$_SESSION['json']['relater'] = $page->id;
}
$page->title = 'Read Entity';
Display::end('@standardFullPage,,entity/read');