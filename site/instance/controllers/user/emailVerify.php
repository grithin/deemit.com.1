<?
$page->title = 'Email Verify';

if(PageTool::verify()){
	Page::saveMessages();
	Http::redirect('/user/');
}
Display::show('standardPage');
