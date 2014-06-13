<?
User::required();

if($id = CRUDController::handle()->create){
	Page::success('Entity Created');
	Page::notice('You can delete your post by voting against it.  However, if someone else votes on your post, deletion by vote will reduce your user significance.');
	Page::saveMessages();
	Http::redirect('read/'.$id);
}

$page->title = 'Create Entity';
Display::show('@standardFullPage,,entity/create');