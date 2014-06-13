<?
User::required();

Debug::out(Db::rows('select count(*) from contractor_signup group by referrer'));

Debug::out(Db::rows('select count(*) from contractor_signup group by zip'));

$contractors = Db::rows('select * from contractor_signup group by email');

foreach($contractors as $contractor){
	$contractor['data'] = unserialize($contractor['data']);
	Debug::out($contractor);
}

