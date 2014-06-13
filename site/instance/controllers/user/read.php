<?

CommentModel::_delete('entity_comment',2);

User::required();
getId();
if(!CRUDController::handle()->return){
	badId();
}

if($_POST['relate']){
	$_SESSION['js']['relate'] = 'entity/relate.js';
	$_SESSION['css']['relate'] = 'entity/relate.css';
	$_SESSION['json']['relater'] = $page->id;
}
$page->title = 'Read User';
Display::end('@standardFullPage,,user/read');