<?
User::required();
getId();
if(!PageTool::read()){
	badId();
}
if(CRUDController::handle()->update){
	Page::success('Update Successful');
	Page::saveMessages();
	Http::redirect('read/'.$page->id);
}

$page->title = 'Update Entity Relation';
Display::end('@standardFullPage,,entity/relation/update');