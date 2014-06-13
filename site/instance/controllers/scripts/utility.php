<?
$class = Page::$in['c'];
$method = Page::$in['m'];
$args = Page::$in['a'];
if(!is_array(Page::$in['a'])){
	$args = array($args);
}
$call = array($class,$method);
if(is_callable($call)){
	call_user_func_array($call,$args);
}else{
	Debug::quit('Unknown function',Page::$in);
}