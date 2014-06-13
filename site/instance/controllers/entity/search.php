<?

if(Page::$in['q']){
	PageTool::read();
}

$page->title = 'Entity Search';
Display::end('@standardFullPage,,entity/search');