<?
class PageTool{
	static function validate(){
		Page::filterAndValidate(array(
			'paypal' => 'email',
		));
		return !Page::errors();
	}
	
	static function update(){
		if(self::validate()){
			Db::update('user',array('paypal'=>Page::$in['paypal']),User::id());
			return true;
		}
	}
	static function read(){
		Page::$data->paypal = Db::row('user',User::id(),'paypal');
	}
}
