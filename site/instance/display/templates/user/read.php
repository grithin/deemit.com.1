<?
DH::dialogIncludes();
?>
<div class="section">
	<div class="title">
		Actions
	</div>
	<div class="content">
		<a class="button" href="../requestDeletion?id=<?=$page->id?>">Request Deletion</a>
<?	if($page->id == User::id()){?>
				<a class="button" href="../update?id=<?=$page->id?>">Update</a>
<?	}?>
	</div>
</div>
<div class="section">
	<div class="title">
		Details
	</div>
	<div class="content">		
		<table class="standard">
			<tbody>
				<tr>
					<td>Display Name</td>
					<td><?=$page->user['display_name']?></td>
				</tr>
				<tr>
					<td>Significance</td>
					<td><?=DH::significance($page->user['significance'],'user',false)?></td>
				</tr>
				<tr>
					<td>Last Login</td>
					<td><?=DH::time($page->user['time_last_login'])?></td>
				</tr>
				<tr>
					<td>Created</td>
					<td><?=DH::time($page->user['time_created'])?></td>
				</tr>
				<tr>
					<td>Public Statement</td>
					<td><?=$page->user['public_statement']?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="section">
	<div class="title">
		Public Statement
	</div>
	<div class="content">
		<?=$page->user['public_statement'] ? $page->user['public_statement'] : 'No public statement'?>
	</div>
</div>

<div class="section">
	<div class="title">
		Relations Created
	</div>
	<div class="content">
<?	if($page->relations){?>
<table class="standard wide">
	<thead>
		<tr>
			<th>Reason Title</th>
			<th>Relater</th>
			<th>Relatee</th>
			<th>For Factor</th>
			<th>Control Factor</th>
			<th>Time Created</th>
		</tr>
	</thead>
	<tbody>
<?		foreach($page->relations as $relation){?>
		<tr>
			<td><?=DH::entityRelation($relation['reason_title'],$relation['id'])?></td>
			<td><?=DH::entity($relation['relater_title'],$relation['relater_id'])?></td>
			<td><?=DH::entity($relation['relatee_title'],$relation['relatee_id'])?></td>
			<td><?=DH::forFactor($relation['for_factor'])?></td>
			<td><?=DH::controlFactor($relation['control_factor'])?></td>
			<td><?=DH::time($relation['time_created'])?></td>
		</tr>
<?		}?>
	</tbody>
</table>
<?	}else{?>
				No Relations
<?	}?>
	</div>
</div>

<div class="section">
	<div class="title">
		Entities Created
	</div>
	<div class="content">
<?	if($page->entities){?>
<table class="standard wide">
	<thead>
		<tr>
			<th>Entity</th>
			<th>Significance</th>
			<th>Time Created</th>
		</tr>
	</thead>
	<tbody>
<?		foreach($page->entities as $entity){?>
		<tr>
			<td><?=DH::entity($entity['title'],$entity['id'])?></td>
			<td><?=$entity['significance']?></td>
			<td><?=DH::time($entity['time_created'])?></td>
		</tr>
<?		}?>
	</tbody>
</table>
<?	}else{?>
				No Entities
<?	}?>
	</div>
</div>
<?=Display::getTemplate('comments')?>