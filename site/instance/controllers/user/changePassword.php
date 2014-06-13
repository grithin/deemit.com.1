<?
User::required();

$page->title = 'Change Password';

if(CRUDController::handle()->update){
	Page::success('Password changed');
}

Display::show('@standardFullPage,,user/changePassword');
