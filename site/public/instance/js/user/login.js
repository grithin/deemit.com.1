$$('*[name="Sign Up"]').addEvent('click',function(e){
	e.stopPropagation()
	$$('*[name="_cmd_update"]').destroy()
	$('login').set('action','signup')
	$('login').submit()
})