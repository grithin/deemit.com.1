<?
class EntityRelation{
	static $base = 2;
	static $factorTotal;
	static function compileRationalWeights(){
		self::$factorTotal = pow(self::$base,10);
		
		Db::query('truncate entity_relation_compile_start');
		Db::query('truncate entity_relation_compile_tmp');
		
		$sql = 'select e.id 
			from entity e left join entity_relation_compile_start ercs on e.id = ercs.___id_relater
		where ercs.id is null';
		
		while($entity = Db::row($sql)){
			self::compileEntityRelationWeights(array($entity));
		}
		/*
		get entities
		loop entities
			if already parsed, next
			find all relations
			add relation values
			follow relation to relatee entity
				recurse with entity prefix to b
		*/
		
		//get log 10 of compiled relations to put on scale of 10
		Db::query('truncate entity_relation_compile');
		$relations = Db::rows('select * from entity_relation_compile_tmp');
		foreach($relations as $relation){
			$reverse = 1;
			if($relation['for_factor'] < 0){
				$reverse = -1;
				$relation['for_factor'] *= -1;
			}
			if($relation['for_factor']){
				$relation['for_factor'] = $reverse * max(min(log($relation['for_factor'],self::$base),10),-10);
			}
			if($relation['control_factor']){
				$relation['control_factor'] = min(log($relation['control_factor'],self::$base),10);
			}
			Db::insert('entity_relation_compile',$relation);
		}
	}
	static $completedEntities = array();
	/**
		By following the paths of entity, related  entities, can apply two principles to ensure correct calculation:
		principles:
			exhaustion:
				when all paths from an entity have been explore fully, don't traverse the entity again
			overlap
				when an entity is already in a path:
					pass by it again only if other entities gain relation factors
						this occurs when E to OE(overlapped entity) is first occurence in path (ie, no multiple E -> OE relations in current path to prevent infinite loop)
					don't refactor for the overlapped entity
	*/
	static function compileEntityRelationWeights($entities,$connections=array()){
		$current = end($entities);
		Db::insertIgnore('entity_relation_compile_start',array('___id_relater'=>$current));
		$relations = Db::rows('select _id_relatee, for_factor, control_factor , significance
			from entity_relation 
			where _id_relater = '.$current.'
				and is_deleted is null');
		foreach($relations as $relation){
			$usedEntities = array();
			$newFactors = false;
			$newConnections = $connections;
			$newConnections[] = array(
					'for_factor'=>$relation['for_factor'],
					'control_factor'=>$relation['control_factor'],
					'significance' => $relation['significance'],
				);
			
			$newEntities = $entities;
			$newEntities[] = $relation['_id_relatee'];
			
			$previousEntities = $entities;
			end($entities);
			$depth = 1;
			//recurse downwards and apply new relation to all lined entities
			while($entity = current($entities)){
				do{
					//don't re-apply relation
					if(Arrays::countIn($entity,$newEntities,2) == 2){
						break;
					}	
					//entity relations were already completely compiled, don't duplicate
					if(self::$completedEntities[$entity]){
						break;
					}
					//don't relate entity to self
					if($entity == $relation['_id_relatee']){
						break;
					}
					//don't relate X to Y in a path that crosses Y to get to Y (is redundant)
					if(in_array($relation['_id_relatee'],$usedEntities)){
						break;
					}
					
					$newFactors = true;
					
					end($newConnections);
					$i = 0;
					$factors = array('for'=>1,'control'=>1,'significance'=>null);
					while(($connection = current($newConnections)) && $i < $depth){
						$factors['for'] *= $connection['for_factor']/10;
						$factors['control'] *= $connection['control_factor']/10;
						//set significance to weakest link in chain
						if($factors['significance'] !== null){
							$factors['significance'] = min($connection['control_factor'],$factors['significance']);
						}else{
							$factors['significance'] = $connection['control_factor'];
						}
						
						prev($newConnections);
						$i++;
					}
					
					
					$reverse = 1;
					if($factors['for'] < 0){
						$reverse = -1;
						$factors['for'] *= -1;
					}
					
					//0 factors are not considerered
					if($factors['for']){
						$factors['for'] = $reverse * pow(self::$factorTotal,$factors['for']);
					}
					if($factors['control']){
						$factors['control'] = pow(self::$factorTotal,$factors['control']);
					}
					
					$insert = array(
							'_id_relater' => $entity,
							'_id_relatee' => $relation['_id_relatee'],
							'for_factor' => $factors['for'],
							'control_factor' => $factors['control'],
							'significance' => $factors['significance'],
						);
					$update = array(
							':for_factor'=>'for_factor + '.$factors['for'],
							':control_factor'=>'control_factor + '.$factors['control'],
							//choose the strongest chain
							':significance' => 'greatest(significance,'.$factors['significance'].')',
						);
					Db::insertUpdate('entity_relation_compile_tmp',$insert,$update);
				} while(0);
				
				$usedEntities[] = $entity;
				prev($entities);
				$depth++;
			}
			
			//if new relations were found, then there might be more new relations upon continue down the path
			if($newFactors){
				//set new entity as the starter, and complete path recursion from there
				self::compileEntityRelationWeights($newEntities,$newConnections);
			}
		}
		//ensure this isn't a overlap completion (vs a root completion)
		if(Arrays::countIn($current,$entities,2) == 1){
			self::$completedEntities[$current] = true;
		}
		
	}
}
