tp.ui.vote = {}
tp.ui.vote.dialog = function(id,type,topic){
	var elements = Elements.from('<div id="voteDialog">\
			<div class="instruction">Vote Direction</div>\
			<div class="option"><span class="button voteOption" data-vote="for">For</span> | <span class="button voteOption" data-vote="against">Against</button></span>\
		</div>')
	
	tp.ui.dialog.make(elements,'Vote')
	$$('.voteOption').addEvent('click',function(e){
		e.stopPropagation()
		var vote = e.target.get('data-vote');
		
		if(!id){
			id = tp.json.id
		}
		
		(new Request({
				url:'/vote',
				data:{type:type,topic:topic,vote:true,value:vote,_cmd_create:true,_id:id},
				onComplete: function(){
					tp.relocate()
				}
			})).send()
	})
}
window.addEvent('domready', function(){
	$$('.voteOnComment').addEvent('click', function(e){
		e.stopPropagation();
		var id = e.target.getParent('.comment').get('data-commentId')
		
		var elements = Elements.from('<div id="voteDialog">\
			<div class="instruction">Vote Type</div>\
			<div class="option"><span class="button voteOption" data-vote="enjoyment">Enjoyment</span> | <span class="button voteOption" data-vote="significance">Significance</span></div>\
		</div>')
		tp.ui.dialog.make(elements,'Vote Options')
		$$('.voteOption').addEvent('click',function(e){
			e.stopPropagation()
			tp.ui.vote.dialog(id,'comment',e.target.get('data-vote'))
		})
	});
	$$('.voteOn').addEvent('click', function(e){
		tp.ui.vote.dialog()
	})
});

