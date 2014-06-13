<?
Form::$values['paypal'] = $page->paypal;
?>

<form action="" method="post">
	<input type="hidden" name="_cmd_update" value="1"/>
	<table class="standard">
		<tbody>
			<tr>
				<?=FormStructure::text('Paypal Account','paypal',null,array('data-help'=>'Comma separated list of keywords. Keywords should be alternative names for the entitiy or essential descriptive terms'))?>
			</tr>
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><?=Form::submit('Update')?></td>
			</tr>
		</tfoot>
	</table>
</form>

