<?

#add instance wide js and css
Display::$tagPrepend = true;
Display::addCss('main.css');

Display::addTopJs('main.js');

if($_SESSION['js']){
	foreach($_SESSION['js'] as $js){
		Display::addTopJs($js);
	}
}
if($_SESSION['css']){
	foreach($_SESSION['css'] as $css){
		Display::addCss($css);
	}
}
if($_SESSION['json']){
	Display::$json = Arrays::merge(Display::$json,$_SESSION['json']);
}
if($userId = User::id()){
	Display::$json['userId'] = $userId;
}


#general system js
$js = array();
foreach(array('general/mootools-core-1.4.5-full-nocompat-yc.js','general/mootools-more-1.4.0.1.js','system/tools.js','system/debug.js','system/ui.js') as $v){
	$js[] = '/'.Config::$x['urlSystemFileToken'].'/js/'.$v;
}
call_user_func_array(array('Display','addTopJs'),$js);
Display::addCss('/'.Config::$x['urlSystemFileToken'].'/css/main.css');
Display::$tagPrepend = false;

$page->resourceModDate = '20110717.1';
Display::$json['messages'] = Page::$messages;

$page->crumbs = $page->crumbs ? $page->crumbs : array();
array_unshift($page->crumbs,array('name'=>'Home','link'=>'/'));

if(!$page->headTitle){
	$page->headTitle = $page->title;
}


//mean deviations
if(Cache::get('significanceMean')){
	foreach(array('user','entity','entity_relation') as $table){
		Display::$json['significance'][$table]['mean'] = Cache::get('significanceMean-'.$table);
		Display::$json['significance'][$table]['deviation'] = Cache::get('significanceMeanDeviation-'.$table);
	}
}

if(!$page->description){
	$page->description = 'Relation network factoring control and for/against';
}
if(!$page->keywords){
	$page->keywords = 'enttiy relation network, relation network';
}