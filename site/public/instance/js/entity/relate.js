window.addEvent('domready',function(){
	var relaterBox = Elements.from('<div id="relaterBoxParent">\
				<form id="relaterForm" action="/entity/relation/create" method="post">\
					<div id="relaterBox">\
						Please Navigate to a 2nd Entity\
						<div id="relaterActions">\
							<input name="relatee" value="" type="hidden" id="relaterRelatee"/>\
							<input name="relater" value="'+tp.json.relater+'" type="hidden" id="relaterRelater"/>\
							<button name="relate" id="relaterRelateButton" value="relate">Relate</button>\
							<button name="cancel" id="relaterCancelButton" value="relate">Cancel</button>\
						</div>\
					</div>\
				</form>\
			</div>')
	
	relaterBox.inject($$('body')[0],'top')
	
	
	$('relaterCancelButton').addEvent('click',function(e){
		(new Request({url: '/entity/relation/create', data:{cancelRelate:1}})).send()
		e.stopPropagation()
		$('relaterBoxParent').destroy()
	})
	
	if(tp.json.id && tp.json.id != tp.json.relater && tp.json.id_type == 'entity'){
		$('relaterRelateButton').addEvent('click',function(e){
			$('relaterRelatee').set('value',tp.json.id)
		})
	} else {
		$('relaterRelateButton').set('disabled',1)
	}
	
})
