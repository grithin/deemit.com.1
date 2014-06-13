<?
DH::editorIncludes();
?>


<form action="" method="post">
	<input type="hidden" name="_cmd_create" value="1"/>
	<?=Form::hidden('relater')?>
	<?=Form::hidden('relatee')?>
	<table class="standard">
		<tbody>
			<tr>
				<td>Relater</td>
				<td><?=$page->relater?></td>
			</tr>
			<tr>
				<td>Relatee</td>
				<td><?=$page->relatee?></td>
			</tr>
			<tr>
				<?=FormStructure::text('Reason Title','reason_title',null,array('data-help'=>'Title describing the reason for the relation'))?>
			</tr>
			<tr>
				<?=FormStructure::select('For Factor','for_factor',$page->forFactors,null,array('data-help'=>'url:/about#forFactor'))?>
			</tr>
			<tr>
				<?=FormStructure::select('Control Factor','control_factor',$page->controlFactors,null,array('data-help'=>'url:/about#controlFactor'))?>
			</tr>
			<tr>
				<?=FormStructure::textarea('Reason Text','reason_text',null,array('class'=>'editor','data-help'=>'The full reason for the relation'))?>
			</tr>
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><?=Form::submit('Create')?></td>
			</tr>
		</tfoot>
	</table>
</form>

