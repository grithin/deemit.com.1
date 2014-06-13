<?
User::required();
getId();
if(!PageTool::read()){
	badId();
}
if(CRUDController::handle()->create){
	Page::success('Request Created');
	Display::end('standardPage');
}

Page::warning('Do not use this utility without good reason');
$page->title = 'Request User Deletion';
Display::end('@standardFullPage,,user/requestDeletion');