<?
class MeanDeviation{
	static function significance(){
		//run through all the users
		$table = 'user';
		$where = array('is_verified'=>1,'is_disabled'=>null);
		$mean = Db::row($table,$where,'AVG(significance)');
		$count = $deviationTotal = 0;
		foreach(DbBatch::get(50,$table,$where,'significance') as $item){
			foreach($item as $item){
				$deviationTotal += abs($item['significance'] - $mean);
				$count++;
			}
		}
		$meanDeviation = @ceil($deviationTotal/$count);
		Cache::set('significanceMean-'.$table,$mean);
		Cache::set('significanceMeanDeviation-'.$table,$meanDeviation);

		//run through all the entities
		$table = 'entity';
		$where = '';
		$mean = Db::row($table,$where,'AVG(significance)');
		$count = $deviationTotal = 0;
		foreach(DbBatch::get(50,$table,$where,'significance') as $item){
			foreach($item as $item){
				$deviationTotal += abs($item['significance'] - $mean);
				$count++;
			}
		}
		$meanDeviation = @ceil($deviationTotal/$count);
		Cache::set('significanceMean-'.$table,$mean);
		Cache::set('significanceMeanDeviation-'.$table,$meanDeviation);

		//run through all the entity relations
		$table = 'entity_relation';
		$where = '';
		$mean = Db::row($table,$where,'AVG(significance)');
		$count = $deviationTotal = 0;
		foreach(DbBatch::get(50,$table,$where,'significance') as $item){
			foreach($item as $item){
				$deviationTotal += abs($item['significance'] - $mean);
				$count++;
			}
		}
		$meanDeviation = @ceil($deviationTotal/$count);
		Cache::set('significanceMean-'.$table,$mean);
		Cache::set('significanceMeanDeviation-'.$table,$meanDeviation);
		
		Cache::set('significanceMean',1);
	}
	static function comments($table,$rowId,$update=false){
		$keys['significance'] = 'stats-significance-'.$table.'-'.$rowId;
		$keys['enjoyment'] = 'stats-enjoyment-'.$table.'-'.$rowId;
		
		if($update || Cache::get($keys['significance'].'-mean') === false){
			$where = array('_id'=>$rowId);
			$means = Db::row($table,$where,'AVG(significance) significance,AVG(enjoyment) enjoyment');
			$counts = $deviationTotals = array('significance'=>0,'enjoyment'=>0);
			foreach(DbBatch::get(50,$table,$where,'significance, enjoyment') as $item){
				foreach($item as $item){
					if($item['significance']){
						$deviationTotals['significance'] += abs($item['significance'] - $means['significance']);
						$counts['significance']++;
					}
					if($item['enjoyment']){
						$deviationTotals['enjoyment'] += abs($item['enjoyment'] - $means['enjoyment']);
						$counts['enjoyment']++;
					}
				}
			}
			$meanDeviations['significance'] = @ceil($deviationTotals['enjoyment']/$counts['enjoyment']);
			Cache::set($keys['significance'].'-mean',$means['significance'],86400);
			Cache::set($keys['significance'].'-deviation',$meanDeviations['significance'],86400);
			
			$meanDeviations['enjoyment'] = @ceil($deviationTotals['enjoyment']/$counts['enjoyment']);
			Cache::set($keys['enjoyment'].'-mean',$means['enjoyment'],86400);
			Cache::set($keys['enjoyment'].'-deviation',$meanDeviations['enjoyment'],86400);
		}
		return array(
			'significance' => array(
				'mean' => Cache::get($keys['significance'].'-mean'),
				'deviation' =>Cache::get($keys['significance'].'-deviation')
			),
			'enjoyment' => array(
				'mean' => Cache::get($keys['enjoyment'].'-mean'),
				'deviation' =>Cache::get($keys['enjoyment'].'-deviation')
			));
	}
}
