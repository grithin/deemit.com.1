//on the premise that there should only be one
tp.ui.dialog = {}
tp.ui.dialog.close = function(){
	if(tp.ui.dialog.box){
		tp.ui.dialog.box.close()
		tp.ui.dialog.box.destroy()
		tp.ui.dialog.box = null
	}
}

tp.ui.dialog.make = function(content,title){
	tp.ui.dialog.close()
	options = {}
	if(title){
		options.title = title
	}
	
	var dialog = new MooDialog(options);
	dialog.setContent(content).open()
	tp.ui.dialog.box = dialog
}
