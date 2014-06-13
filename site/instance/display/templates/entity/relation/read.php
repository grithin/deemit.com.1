<?
DH::dialogIncludes();
?>


<table class="standard">
	<tbody>
<?	if($page->relation['user_id'] == User::id()){?>
		<tr>
			<td colspan="2" style="text-align:center">
				<a  href="../update?id=<?=$page->id?>">Update</a>
			</td>
		</tr>
<?	}?>

		<tr>
			<td>Relater</td>
			<td><?=DH::entity($page->relater['title'],$page->relater['id'])?></td>
		</tr>
		<tr>
			<td>Relatee</td>
			<td><?=DH::entity($page->relatee['title'],$page->relatee['id'])?></td>
		</tr>
		<tr>
			<td>Reason Title</td>
			<td><?=$page->relation['reason_title']?></td>
		</tr>
		<tr>
			<td>For Factor</td>
			<td><?=DH::forFactor($page->relation['for_factor'])?></td>
		</tr>
		<tr>
			<td>Control Factor</td>
			<td><?=DH::controlFactor($page->relation['control_factor'])?></td>
		</tr>
		<tr>
			<td>Significance</td>
			<td><?=DH::significance($page->relation['significance'],'entity_relation')?></td>
		</tr>
		<tr>
			<td>Creating User</td>
			<td><?=DH::user($page->relation['user_name'],$page->relation['user_id'])?></td>
		</tr>
	</tbody>
</table>

<div class="section">
	<div class="title">
		Reason
	</div>
	<div class="content">
		<?=$page->relation['reason_text']?>
	</div>
</div>


<?=Display::getTemplate('comments')?>