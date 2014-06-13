<?
User::required();
if($_SESSION['js']['relate']){
	unset($_SESSION['js']['relate'],$_SESSION['css']['relate'],$_SESSION['json']['relater']);
}
if($_POST['cancelRelate']){
	exit;
}

if($id = CRUDController::handle()->create){
	Page::success('Relation Created');
	Page::notice('You can delete your post by voting against it.  However, if someone else votes on your post, deletion by vote will reduce your user significance.');
	Page::saveMessages();
	Http::redirect('read/'.$id);
}

$page->title = 'Create Entity Relation';
Display::end('@standardFullPage,,entity/relation/create');