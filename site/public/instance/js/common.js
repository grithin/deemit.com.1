$(function(){
	$('a.delete').each(function(){
		var location = $(this).attr('href');
		$(this).attr('href','#');
		$(this).attr('data-location',location);
	}).click(function(e){
		e.preventDefault();
		if(confirm('Are you sure you want to delete this?')){
			tp.relocate($(this).attr('data-location'));
		}
	})
});