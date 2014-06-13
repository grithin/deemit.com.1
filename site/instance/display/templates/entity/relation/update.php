<?
DH::editorIncludes();
Form::$values = $page->relation;
?>


<form action="" method="post">
	<input type="hidden" name="_cmd_update" value="1"/>
	<table class="standard">
		<tbody>
			<tr>
				<?=FormStructure::textarea('Reason Text','reason_text',null,array('class'=>'editor','data-help'=>'The full reason for the relation'))?>
			</tr>
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><?=Form::submit('Update')?></td>
			</tr>
		</tfoot>
	</table>
</form>

