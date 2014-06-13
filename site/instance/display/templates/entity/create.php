<?
DH::editorIncludes();
?>

<form action="" method="post">
	<input type="hidden" name="_cmd_create" value="1"/>
	<table class="standard">
		<tbody>
			<tr>
				<?=FormStructure::text('Title','title',null,array('data-help'=>'Should be able to identify the entity based on the title'))?>
			</tr>
			<tr>
				<?=FormStructure::text('Keywords','keywords',null,array('data-help'=>'Comma separated list of keywords. Keywords should be alternative names for the entitiy or essential descriptive terms'))?>
			</tr>
			<tr>
				<?=FormStructure::select('Type','type_id',$page->entity_types,null,array('none'=>'Select Type','capitalize'=>true,'data-help'=>'url:types'))?>
			</tr>
			<tr>
				<?=FormStructure::textarea('Description','description',null,array('class'=>'editor','data-help'=>'Please include any references to help distinguish the entity'))?>
			</tr>
			
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><?=Form::submit('Create')?></td>
			</tr>
		</tfoot>
	</table>
</form>

