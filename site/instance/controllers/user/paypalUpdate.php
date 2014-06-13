<?
User::required();

$page->title = 'Paypal Update';

if(CRUDController::handle()->update){
	Page::success('Paypal Account Updated');
}

Display::show('@standardFullPage,,user/paypalUpdate');
