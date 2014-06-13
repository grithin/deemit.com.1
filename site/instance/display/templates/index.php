<style>
	.about p{
		text-shadow : 1px 1px 1px silver;
		font-size:12pt;
		padding:10px;
	}
</style>
<div class="section">
	<div class="title">
		About
	</div>
	<div class="content about">
		<p>
			Deemit is a user created relation network.  It is designed to show things like which politicians were for the patriot act and which organizations control which politicians.  The Deemit system itself creates  "<a href="/about#compiledRelations">compiled relations</a>" between entities to help see indirect relations between people and organizations.  To learn more, see the <a href="/about">about page</a>
		</p>
	</div>
</div>

<div class="section">
	<div class="title">
		Activity
	</div>
	<div class="content">

<?	foreach($page->order as $group){?>
<?		if($page->highlights['entity_relation'][$group]){?>
<div class="section">
	<div class="title">
		Relation: <?=ucwords($group)?>
	</div>
	<div class="content">
<table class="standard wide" style="text-align:center">
	<thead>
		<tr>
			<th>Relater</th>
			<th>Relatee</th>
			<th>For Factor</th>
			<th>Control Factor</th>
			<th>Reason Title</th>
			<th>Significance</th>
			<th>User</th>
			<th>User Significance</th>
		</tr>
	</thead>
	<tbody>
	
<?			foreach($page->highlights['entity_relation'][$group] as $result){?>
		<tr>
			<td><?=DH::entity($result['relater_title'],$result['relater_id'])?></td>
			<td><?=DH::entity($result['relatee_title'],$result['relatee_id'])?></td>
			<td><?=DH::forFactor($result['for_factor'])?></td>
			<td><?=DH::controlFactor($result['control_factor'])?></td>
			<td><?=DH::entity($result['title'],$result['id'])?></td>
			<td><?=DH::significance($result['significance'],'entity',false)?></td>
			<td><?=DH::user($result['user_name'],$result['user_id'])?></td>
			<td><?=DH::significance($result['user_significance'],'user',false)?></td>
		</tr>
<?			}?>
	</tbody>
</table>

	</div>
</div>
<?		}?>
<?		if($page->highlights['entity'][$group]){?>
<div class="section">
	<div class="title">
		Entity: <?=ucwords($group)?>
	</div>
	<div class="content">

<table class="standard wide" style="text-align:center">
	<thead>
		<tr>
			<th>Title</th>
			<th>Type</th>
			<th>Keywords</th>
			<th>Significance</th>
			<th>User</th>
			<th>User Significance</th>
		</tr>
	</thead>
	<tbody>
	
<?			foreach($page->highlights['entity'][$group] as $result){?>
		<tr>
			<td><?=DH::entity($result['title'],$result['id'])?></td>
			<td><?=$result['type_name']?></td>
			<td title="<?=$result['weight']?>"><?=$result['keywords']?></td>
			<td><?=DH::significance($result['significance'],'entity',false)?></td>
			<td><?=DH::user($result['user_name'],$result['user_id'])?></td>
			<td><?=DH::significance($result['user_significance'],'user',false)?></td>
		</tr>
<?			}?>
	</tbody>
</table>

	</div>
</div>

<?		}?>
<?	}?>

	</div>
</div>