window.addEvent('domready', function(){
	$$('textarea.editor').mooEditable({
			actions: 'bold italic underline strikethrough | justifyleft justifyright justifycenter justifyfull | insertunorderedlist insertorderedlist indent outdent | undo redo | createlink unlink | urlimage | toggleview'
		});
});