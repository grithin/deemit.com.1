<?
if($_SESSION['userId']){
	Http::redirect('/');
}

$page->title = 'login';
if(CRUDController::handle()->update){
	$url = $_COOKIE['url'] ? $_COOKIE['url'] : '/user/';
	Cookie::remove('url');
	Http::redirect($url);
}

Display::show('standardPage,,user/login');
