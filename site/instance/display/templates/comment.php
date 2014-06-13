<?
DH::editorIncludes()
?>

<form action="" method="post">
	<input type="hidden" name="_cmd_create" value="1"/>
	<?=Form::hidden('_id',Page::$in['_id'])?>
	<table class="standard">
		<tbody>
			<tr>
				<td><?=ucwords(PageTool::$type)?></td>
				<td><?=call_user_func_array(PageTool::$renderer,array($page->object['title'],Page::$in['_id']))?></td>
			</tr>
			<tr>
				<?=FormStructure::text('Title','title',null,array('data-help'=>'Way to give people idea on what comment is about'))?>
			</tr>
			<tr>
				<?=FormStructure::textarea('Text','text',null,array('class'=>'editor','data-help'=>'Please restrict to useful information'))?>
			</tr>
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><?=Form::submit('Create',null,array('class'=>'button'))?></td>
			</tr>
		</tfoot>
	</table>
</form>