<?
//should be run every day

//update user login times with session table (for continued sessions)
Db::query('SET time_zone = \'+00:00\'');
Db::query('update user u, session s set u.time_last_login = FROM_UNIXTIME(s.time) where u.id = s.user_id');

$time = i()->Time();
$timeUnix = $time->unix();

$day = 60 * 60 * 24;
$week = $day * 7;
$month = $day * 30;
$year = $day * 365;

$significanceMeanCalculated = Cache::get('significanceMean');
if($significanceMeanCalculated){
	foreach(array('user','entity','entity_relation') as $type){
		$significanceMean[$type]['mean'] = Cache::get('significanceMean-'.$type);
		$significanceMean[$type]['deviation'] = Cache::get('significanceMeanDeviation-'.$type);
	}
}

//run through all the users
foreach(DbBatch::get(50,'user',array('is_verified'=>1),'id,is_disabled,time_last_login,time_created,significance') as $users){
	foreach($users as $user){
		//user account age related
		$startUnix = i()->Time($user['time_created'])->unix();
		$ageSeconds = $timeUnix - $startUnix;
		$existence = array(
				'day' => floor($ageSeconds/$day),
				'week' => floor($ageSeconds/$week),
				'month' => floor($ageSeconds/$month),
				'year' => floor($ageSeconds/$year),
			);
		
		$lastLogin = i()->Time($user['time_last_login']);
		//user hasn't logged in in over 7 days, decrease signifcance and don't add significance
		if($lastLogin->diff($time)->d > 7){
			//take off at least 1%
			if($user['significance'] > 1){
				$adjustment = -1 * max(floor($user['significance']/100),1);
				$type = 'week';
				$key = 'inactivity:'.$type.':'.$existence[$type];
				UserSignificance::addReference('user',$user['id'],$key,$adjustment,'user inactivity '.$type,$user['id']);
			}
			continue;
		}
		
		$type = 'day';
		if($user['significance'] >= 10 && $existence[$type] && $existence[$type] <= 3){
			$key = 'survival:'.$type.':'.$existence[$type];
			UserSignificance::addReference('user',$user['id'],$key,1,'user survived '.$type,$user['id']);
		}
		$type = 'week';
		if($existence[$type] && $existence[$type] <= 6){
			$key = 'survival:'.$type.':'.$existence[$type];
			UserSignificance::addReference('user',$user['id'],$key,1,'user survived '.$type,$user['id']);
		}
		$type = 'month';
		if($existence[$type] && $existence[$type] <= 12){
			$key = 'survival:'.$type.':'.$existence[$type];
			UserSignificance::addReference('user',$user['id'],$key,2,'user survived '.$type,$user['id']);
		}
		$type = 'year';
		if($existence[$type]){
			$key = 'survival:'.$type.':'.$existence[$type];
			UserSignificance::addReference('user',$user['id'],$key,10,'user survived '.$type,$user['id']);
		}
		
		$baseWhere = array('user_id'=>$user['id']);
		$where = array('significance?>=' => 20) + $baseWhere;
		
		//entity related
		$table = 'entity';
		foreach(DbBatch::get(50,$table,$where,'significance, time_created, id') as $posts){
			foreach($posts as $post){
				$startUnix = i()->Time($post['time_created'])->unix();
				$ageSeconds = $timeUnix - $startUnix;
				$existence = array(
						'day' => floor($ageSeconds/$day),
						'week' => floor($ageSeconds/$week),
						'month' => floor($ageSeconds/$month),
						'year' => floor($ageSeconds/$year),
					);
				
				$type = 'day';
				if($existence[$type] && $existence[$type] <= 3){
					$key = 'survival:'.$type.':'.$existence[$type];
					UserSignificance::addReference($table,$post['id'],$key,1,'entity survived '.$type,$user['id']);
				}
				$type = 'week';
				if($existence[$type] && $existence[$type] <= 6){
					$key = 'survival:'.$type.':'.$existence[$type];
					UserSignificance::addReference($table,$post['id'],$key,1,'entity survived '.$type,$user['id']);
				}
				$type = 'month';
				if($existence[$type] && $existence[$type] <= 12){
					$key = 'survival:'.$type.':'.$existence[$type];
					UserSignificance::addReference($table,$post['id'],$key,2,'entity survived '.$type,$user['id']);
				}
				$type = 'year';
				if($existence[$type]){
					$key = 'survival:'.$type.':'.$existence[$type];
					UserSignificance::addReference($table,$post['id'],$key,10,'entity survived '.$type,$user['id']);
				}
				
			
				if($significanceMeanCalculated){
					$deviations = ($post['significance'] - $significanceMean[$table]['mean'])/$significanceMean[$table]['deviation'];
					if($deviations > 2){
						$type = 'week';
						if($existence[$type]){
							$key = 'thrived+:'.$type.':'.$existence[$type];
							UserSignificance::addReference($table,$post['id'],$key,4,'entity thrived '.$type,$user['id']);
						}
					}elseif($deviations > 1){
						$type = 'week';
						if($existence[$type]){
							$key = 'thrived:'.$type.':'.$existence[$type];
							UserSignificance::addReference($table,$post['id'],$key,2,'entity thrived '.$type,$user['id']);
						}
					}
				}
			}
		}
		
		//entity relation related
		$table = 'entity_relation';
		foreach(DbBatch::get(50,$table,$where,'significance, time_created, id') as $posts){
			foreach($posts as $post){
				$startUnix = i()->Time($post['time_created'])->unix();
				$ageSeconds = $timeUnix - $startUnix;
				$existence = array(
						'day' => floor($ageSeconds/$day),
						'week' => floor($ageSeconds/$week),
						'month' => floor($ageSeconds/$month),
						'year' => floor($ageSeconds/$year),
					);

				
				$type = 'day';
				if($existence[$type] && $existence[$type] <= 3){
					$key = 'survival:'.$type.':'.$existence[$type];
					UserSignificance::addReference($table,$post['id'],$key,1,'entity relation survived '.$type,$user['id']);
				}
				$type = 'week';
				if($existence[$type] && $existence[$type] <= 6){
					$key = 'survival:'.$type.':'.$existence[$type];
					UserSignificance::addReference($table,$post['id'],$key,1,'entity relation survived '.$type,$user['id']);
				}
				$type = 'month';
				if($existence[$type] && $existence[$type] <= 12){
					$key = 'survival:'.$type.':'.$existence[$type];
					UserSignificance::addReference($table,$post['id'],$key,2,'entity relation survived '.$type,$user['id']);
				}
				$type = 'year';
				if($existence[$type]){
					$key = 'survival:'.$type.':'.$existence[$type];
					UserSignificance::addReference($table,$post['id'],$key,10,'entity relation survived '.$type,$user['id']);
				}
				
			
				if($significanceMeanCalculated){
					$deviations = ($post['significance'] - $significanceMean[$table]['mean'])/$significanceMean[$table]['deviation'];
					if($deviations > 2){
						$type = 'week';
						if($existence[$type]){
							$key = 'thrived+:'.$type.':'.$existence[$type];
							UserSignificance::addReference($table,$post['id'],$key,4,'entity relation thrived '.$type,$user['id']);
						}
					}elseif($deviations > 1){
						$type = 'week';
						if($existence[$type]){
							$key = 'thrived:'.$type.':'.$existence[$type];
							UserSignificance::addReference($table,$post['id'],$key,2,'entity relation thrived '.$type,$user['id']);
						}
					}
				}
			}
		}
	}
}