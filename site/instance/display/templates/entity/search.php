
<form action="" method="get">
	<table class="standard">
		<tbody>
			<tr>
				<?=FormStructure::text('Search','q',null,array('class'=>'editor','data-help'=>'url:searchHelp'))?>
			</tr>
		</tbody>
		<tfoot>
			<tr class="submitRow">
				<td colspan="2" class="formAction"><input type="submit" value="Search"/></td>
			</tr>
		</tfoot>
	</table>
</form>

<?	if(Page::$in['q']){?>
<div id="searchResults">
<?		if($page->results){?>
	<table class="standard pagedTable wide" style="text-align:center">
		<thead>
			<tr>
				<th>Title</th>
				<th>Type</th>
				<th>Keywords</th>
				<th>Entity Significance</th>
				<th>User</th>
				<th>User Significance</th>
			</tr>
		</thead>
		<tbody>
<?			foreach($page->results as $result){?>
			<tr>
				<td><?=DH::entity($result['title'],$result['id'])?></td>
				<td><?=$result['type_name']?></td>
				<td title="<?=$result['weight']?>"><?=$result['keywords']?></td>
				<td><?=DH::significance($result['significance'],'entity',false)?></td>
				<td><?=DH::user($result['user_name'],$result['user_id'])?></td>
				<td><?=DH::significance($result['user_significance'],'user',false)?></td>
			</tr>
<?			}?>
		</tbody>
		
	</table>
<?		}else{?>
	No Results
<?		}?>
</div>
<?	}?>