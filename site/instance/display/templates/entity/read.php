<?
DH::dialogIncludes();
?>
<?	if($page->entity['user_id'] == User::id()){?>
<div class="section">
	<div class="title">
		Actions
	</div>
	<div class="content">
		<a class="button" href="../update?id=<?=$page->id?>">Update</a>
	</div>
</div>
<?	}?>
<div class="section">
	<div class="title">
		Details
	</div>
	<div class="content">		
		<table class="standard">
			<tbody>
				<tr>
					<td>Title</td>
					<td><?=$page->entity['title']?></td>
				</tr>
				<tr>
					<td>Keywords</td>
					<td><?=$page->entity['keywords']?></td>
				</tr>
				<tr>
					<td>Type</td>
					<td><?=$page->entity['type_name']?></td>
				</tr>
				<tr>
					<td>Significance</td>
					<td><?=DH::significance($page->entity['significance'],'entity')?></td>
				</tr>
				<tr>
					<td>Creating User</td>
					<td><?=DH::user($page->entity['user_name'],$page->entity['user_id'])?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="section">
	<div class="title">
		Description
	</div>
	<div class="content">
		<?=$page->entity['description']?>
	</div>
</div>

<div class="section">
	<div class="title">
		Relations
	</div>
	<div class="content">
		<a class="button" href="?relate=true">Add Relation</a>
		<div class="section">
			<div class="title" data-help="url:/about#compiledRelations">
			Compiled Relatees
			</div>
			<div class="content">
<?	if($page->cRelatees){?>
<table class="standard wide">
	<thead>
		<tr>
			<th>Entity</th>
			<th>For Factor</th>
			<th>Control Factor</th>
			<th>Significance</th>
		</tr>
	</thead>
	<tbody>
<?		foreach($page->cRelatees as $relatee){?>
		<tr>
			<td><?=DH::entity($relatee['entity_title'],$relatee['entity_id'])?></td>
			<td><?=DH::forFactor($relatee['for_factor'])?></td>
			<td><?=DH::controlFactor($relatee['control_factor'])?></td>
			<td><?=DH::significance($page->entity['significance'],'entity_relation',false)?></td>
		</tr>
<?		}?>
	</tbody>
</table>
<?	}else{?>
				No Relatees
<?	}?>
			</div>
		</div>
		
		
		<div class="section">
			<div class="title" data-help="url:/about#compiledRelations">
			Compiled Relaters
			</div>
			<div class="content">
<?	if($page->cRelaters){?>
<table class="standard wide">
	<thead>
		<tr>
			<th>Entity</th>
			<th>For Factor</th>
			<th>Control Factor</th>
			<th>Significance</th>
		</tr>
	</thead>
	<tbody>
<?		foreach($page->cRelaters as $relatee){?>
		<tr>
			<td><?=DH::entity($relatee['entity_title'],$relatee['entity_id'])?></td>
			<td><?=DH::forFactor($relatee['for_factor'])?></td>
			<td><?=DH::controlFactor($relatee['control_factor'])?></td>
			<td><?=DH::significance($page->entity['significance'],'entity_relation',false)?></td>
		</tr>
<?		}?>
	</tbody>
</table>
<?	}else{?>
				No Relaters
<?	}?>
			</div>
		</div>
		
		
		<div class="section">
			<div class="title" data-help="url:/about#directRelations">
			Direct Relatees
			</div>
			<div class="content">
<?	if($page->relatees){?>
<table class="standard wide">
	<thead>
		<tr>
			<th>Entity</th>
			<th>Reason</th>
			<th>For Factor</th>
			<th>Control Factor</th>
			<th>Significance</th>
			<th>Time Created</th>
		</tr>
	</thead>
	<tbody>
<?		foreach($page->relatees as $relatee){?>
		<tr>
			<td><?=DH::entity($relatee['entity_title'],$relatee['entity_id'])?></td>
			<td><?=DH::entityRelation($relatee['reason_title'],$relatee['relation_id'])?></td>
			<td><?=DH::forFactor($relatee['for_factor'])?></td>
			<td><?=DH::controlFactor($relatee['control_factor'])?></td>
			<td><?=DH::significance($page->entity['significance'],'entity_relation',false)?></td>
			<td><?=DH::time($relatee['time_created'])?></td>
		</tr>
<?		}?>
	</tbody>
</table>
<?	}else{?>
				No Relatees
<?	}?>
			</div>
		</div>
		<div class="section">
			<div class="title" data-help="url:/about#directRelations">
			Direct Relaters
			</div>
			<div class="content">
<?	if($page->relaters){?>
<table class="standard wide">
	<thead>
		<tr>
			<th>Entity</th>
			<th>Reason</th>
			<th>For Factor</th>
			<th>Control Factor</th>
			<th>Significance</th>
			<th>Time Created</th>
		</tr>
	</thead>
	<tbody>
<?		foreach($page->relaters as $relater){?>
		<tr>
			<td><?=DH::entity($relater['entity_title'],$relater['entity_id'])?></td>
			<td><?=DH::entityRelation($relater['reason_title'],$relater['relation_id'])?></td>
			<td><?=DH::forFactor($relater['for_factor'])?></td>
			<td><?=DH::controlFactor($relater['control_factor'])?></td>
			<td><?=DH::significance($page->entity['significance'],'entity_relation',false)?></td>
			<td><?=DH::time($relater['time_created'])?></td>
		</tr>
<?		}?>
	</tbody>
</table>
<?	}else{?>
				No Relaters
<?	}?>
			</div>
		</div>

<?=Display::getTemplate('comments')?>