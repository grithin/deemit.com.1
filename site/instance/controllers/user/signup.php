<?
$page->title = 'Sign Up';

if(CRUDController::handle()->create){
	Page::success('Signup form submitted');
	Page::success('Email sent to provided address');
	Page::notice('Please confirm your email address using the link in the email we sent');
	Display::show('standardPage');
	exit;
}
Display::show('@standardFullPage,,user/signup');
