<form action="" method="post">
	<input type="hidden" name="_cmd_create" value="1"/>
	<?=Form::hidden('referrer',$_SERVER['HTTP_REFERER'])?>
	<table class="standard">
		<tbody>
			<tr>
				<?=FormStructure::text('First Name','first_name')?>
			</tr>
			<tr>
				<?=FormStructure::text('Last Name','last_name')?>
			</tr>
			<tr>
				<?=FormStructure::text('Birth Date','time_born',null,array('data-help'=>'Pretty much any format'))?>
			</tr>
			<tr>
				<?=FormStructure::text('Email','email')?>
			</tr>
			<tr>
				<?=FormStructure::password('Password','password')?>
			</tr>
			<tr>
				<?=FormStructure::text('Display Name','display_name')?>
			</tr>
			<tr>
				<?=FormStructure::fieldColumns('agree','Agree to Terms',Form::checkbox('agree').' <a class="newTab" href="/user/tou">Terms of Use</a>')?>
			</tr>
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><input type="submit" value="Signup"/></td>
			</tr>
		</tfoot>
	</table>
</form>

