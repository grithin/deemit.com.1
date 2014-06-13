<?
User::required();

if(count(RequestHandler::$urlTokens) == 1){
	Page::error('System hacked; now go away');
	Display::end('standardPage');
}

if($comment = CRUDController::handle()->create){
	Page::success('Comment Created');
	Page::saveMessages();
	Http::redirect('read/'.$comment['_id']);
}
$page->title = 'Create Comment';
Display::end('@standardFullPage,,comment');