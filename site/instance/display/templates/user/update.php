<?
DH::editorIncludes();
Form::$values = $page->user;
?>

<form action="" method="post">
	<input type="hidden" name="_cmd_update" value="1"/>
	<table class="standard">
		<tbody>
			<tr>
				<?=FormStructure::textarea('Public Statement','public_statement',null,array('class'=>'editor','data-help'=>'What you want others to know about you (potentially, ways of contacting)'))?>
			</tr>
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><?=Form::submit('Update')?></td>
			</tr>
		</tfoot>
	</table>
</form>

