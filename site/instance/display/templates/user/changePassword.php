<form action="" method="post">
	<input type="hidden" name="_cmd_update" value="1"/>
	<table class="standard">
		<tbody>
			<tr>
				<?=FormStructure::password('Current Password','current')?>
			</tr>
			<tr>
				<?=FormStructure::password('New Password','new')?>
			</tr>
			<tr>
				<?=FormStructure::password('Retype New Password','newAgain')?>
			</tr>
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><?=Form::submit('Update')?></td>
			</tr>
		</tfoot>
	</table>
</form>
