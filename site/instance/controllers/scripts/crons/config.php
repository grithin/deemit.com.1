<?
#std cron: m h  dom mon dow
Cron::$list = array(
		array('1 * * * *','compileRelations'),
		array('1 1 * * *','significanceMean'),
		array('1 3 * * *','userPoints'),
	);