<?
DH::editorIncludes();
?>

<form action="" method="post">
	<input type="hidden" name="_cmd_create" value="1"/>
	<table class="standard">
		<tbody>
			<tr>
				<?=FormStructure::textarea('Reason','reason',null,array('class'=>'editor','data-help'=>'Please include references is possible'))?>
			</tr>
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><?=Form::submit('Create')?></td>
			</tr>
		</tfoot>
	</table>
</form>

