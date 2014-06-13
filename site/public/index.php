<?
//if run by command line, argc (count) should always be >= 1, otherwise, if not run by command line, should not exist.
if($argc){
	$config['inScript'] = true;
	
	$args = getopt('p:q:m:c:',array('path:','query:','mode:','config:','cookie:'));
	
	$config['envMode'] = $args['mode'] ? $args['mode'] : $args['m'];
	
	$_SERVER['QUERY_STRING'] = $args['query'] ? $args['query'] : $args['q'];
	parse_str($_SERVER['QUERY_STRING'],$_GET);
	
	if($args['cookie']){
		parse_str($args['cookie'],$_COOKIE);
	}
	
	//system depends completely on request uri for path request handling
	$_SERVER['REQUEST_URI'] = $args['path'] ? $args['path'] : $args['p'];
	
	$configFile = $args['config'] ? $args['config'] : $args['c'];
	require_once ($configFile ? $configFile : __DIR__.'/../instance/config.php');
	
}else{
	$config['envMode'] = getenv('mode');
	require_once __DIR__.'/../instance/config.php';
}

//if not defined in the config
if(!$config['instanceLocation']){
	$config['instanceLocation'] = realpath(dirname(__FILE__).'/../');
}
$config['systemLocation'] = $config['systemLocation'] ? realpath($config['systemLocation']) : $config['instanceLocation'];

require_once $config['systemLocation'].'/system/preloading/preloader.php';
