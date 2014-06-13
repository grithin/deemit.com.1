tp.ui = {
//+	pageSorting {
	sortTable: function(event){
		var field = event.target.get('data-sortField');
		var order = event.target.get('data-sortOrder');
		if(!order || order == 'desc'){
			order = 'asc'
		}else{
			order = 'desc'
		}
		
		if(event.shift){
			tp.ui.updateSorts({field:field,order:order},0)
		}else{
			tp.ui.updateSorts({field:field,order:order},0,'replace')
		}
		tp.relocate(tp.ui.getSortUrl());
	},
	updateSorts: function(sort,page,type){
		if(page != null){
			tp.sort.page = page
		}
		if(sort != null){
			if(type == 'replace'){
				tp.sort.sorts = {}
				tp.sort.sorts[sort.field] = sort.order
			}else{
				var found = false
				Object.some(tp.sort.sorts,function(order,field){
					if(field == sort.field){
						tp.sort.sorts[field] = sort.order
						found = true
						return true
					}
				})
				if(!found){
					tp.sort.sorts[field] = order
				}
			}
		}
	},
	getSortUrl: function(url){
		url = tp.urlQueryFilter('_so',url)
		url = tp.urlQueryFilter('_sf',url)
		url = tp.urlQueryFilter('_p',url)
		if(tp.sort.sorts){
			Object.each(tp.sort.sorts,function(order,field){
				var pairs = [['_sf',field],['_so',order]]
				url = tp.appendsUrl(pairs,url)
			})
			url = tp.appendUrl('_p',tp.sort.page,url)
		}
		return url
	},
//+	}
//+	System Messages {
	insertMessage: function(message){
		var type = message.context[0]
		var primaryContext = message.context[1]
		if(message.name){
			var fieldDisplayElement = $$('*[data-fieldDisplay='+message.name+']')
			
			if(fieldDisplayElement.length > 0){
				var fieldDisplay = $$('*[data-fieldDisplay='+message.name+']').get('text')
			}else{
				var fieldDisplay = message.name
			}
			$$('*[data-fieldContainer='+message.name+']').addClass(type+'Highlight')
			message.content = message.content.replace(/\{_FIELD_\}/g,'"'+fieldDisplay+'"');
		}
		
		var ele = new Element('div',{html:message.content,class:'message'+' '+type})
		$(primaryContext+'MsgBox').adopt(ele)	
	}
//+	}
}



window.addEvent('domready',function(){
	if(tp.json){
//+	handle system messages{
		if(tp.json.messages){
			Object.each(tp.json.messages,function(message){
				tp.ui.insertMessage(message)
			})
		}
//+	}
//+	handle paging and sorting{
//+		sorting{
		tp.sort = tp.json.sort ? tp.json.sort : {}
		tp.sort.sorts = tp.sort.sorts ? tp.sort.sorts : {}
		
		$$('.sortTable:not(.inlineSort)').each(function(table){
			Object.each(tp.sort.sorts,function(order,field){
				var row = $$('th[data-sortField="'+field+'"]')
				if(order == 'asc'){
					row.addClass('sortAsc')
				}else{
					row.addClass('sortDesc')
				}
				row.set('data-sortOrder',order)
			})
			table.getElements('th[data-sortField]').addEvent('click',tp.ui.sortTable);
//+		}
		})
		
//+		paging{
		if(tp.sort.total > tp.sort.perPage){
			var page = tp.sort.page + 1
			var paginationDiv = $('paginator');
			if(!paginationDiv){
				//if no pagination div found, create on on appropriate table
				var sortTables = $$('.sortTable,.pagedTable')
				if(sortTables.length > 0){
					table = sortTables[0]
					var tfoot = table.getElement('>tfoot')
					if(!tfoot){
						var tfoot = new Element('tfoot',{class:'paging'})
						tfoot.inject(table,'bottom');
					}
					
					var columns = table.getElements('> thead > tr > th').length
					var pagination = Elements.from("<tr>\
						<td colspan='"+columns+"'>\
							<div id='paginator' class='pagination'>\
							</div>\
						</div>")
					pagination.inject(tfoot,'bottom')
					var paginationDiv = pagination.getElement('.pagination')[0]
				}else{
					alert('Pagination location not found')
				}
			}
			
			
			
			//first and prev
			var disabled = page == 1 ? ' disabled' : ''
			var elements = Elements.from('<div class="btn first'+disabled+'">First</div>\
						<div class="btn prev'+disabled+'">Prev</div>')
			
			elements.inject(paginationDiv,'bottom')
			
			//pages
			var lastPage = (tp.sort.total/tp.sort.perPage).ceil()
			var context = 4;
			var projectedStart = page - context
			var start = projectedStart.limit(1,lastPage-4)
			var beyondLower = start - projectedStart
			var projectedEnd = page + context
			var end = projectedEnd.limit(1,lastPage)
			var beyondUpper = projectedEnd - end
			start = (start - beyondUpper).limit(1,lastPage)
			end = (end + beyondLower).limit(1,lastPage)
			var pages = []
			for(var i=start;i <= end; i++){
				var current = i == page ? ' current' : ''
				pages.push('<div class="btn pg'+current+'">'+i+'</div>')
			}
			var elements = Elements.from(pages.join(' '))
			elements.inject(paginationDiv,'bottom')
			
			
			//last and next
			var disabled = page == lastPage ? ' disabled' : ''
			var elements = Elements.from('<div class="btn next'+disabled+'">Next</div>\
						<div class="btn last'+disabled+'">Last</div>')
			elements.inject(paginationDiv,'bottom')
			
			//range
			var elements = Elements.from('<div class="range">\
					<span class="current">'+start+' - '+end+'</span>\
					<span>of</span>\
					<span class="total">'+lastPage+' ['+tp.sort.total+']</span>\
				</div>')
			elements.inject(paginationDiv,'bottom')
			
			
			//go to
			var elements = Elements.from("<div class='direct'>\
							<input type='text' name='directPg' value='"+page+"'/>\
							<div class='btn go'>Go</div>\
						</div>")
			elements.inject(paginationDiv,'bottom')
			
			paginationDiv.getElements('.btn:not(.disabled)').addEvent('click',function(event){
				var page = tp.sort.page + 1
				if(event.target.hasClass('pg')){
					page = event.target.get('text')
				}else if(event.target.hasClass('next')){
					page = page + 1
				}else if(event.target.hasClass('last')){
					page = (tp.sort.total/tp.sort.perPage).ceil()
				}else if(event.target.hasClass('first')){
					page = 1
				}else if(event.target.hasClass('prev')){
					page = page - 1
				}else if(event.target.hasClass('go')){
					page = Number.from($$('#paginator input').get('value')).toInt().abs()
				}
				tp.ui.updateSorts(null,page)
				tp.relocate(tp.ui.getSortUrl());
			})
		}
//+		}
//+	}
	}
//+	tool tips {
	$$('*[data-help]').each(function(tooltippedElement){
		//var fieldDisplayElement = $$('*[data-fieldDisplay='+field+']')
		var tag = tooltippedElement.get('tag')
		if(tag == 'input' || tag == 'select' || tag == 'textarea'){
			var field = tooltippedElement.get('name')
			var relativeElement = $$('*[data-fieldDisplay="'+field+'"]')[0]
			var relativeName = 'bottom'
		}else{
			var relativeElement = tooltippedElement
			if(tag == 'span'){
				var relativeName = 'after'
			}else{
				var relativeName = 'bottom'
			}
			
		}
		var marker = new Element('span',{text:'[?]',class:'tooltipMarker'})
		marker.set('data-tooltip',tooltippedElement.get('data-help'))
		marker.inject(relativeElement,relativeName)
	})
	var tooltipMakerCount = 0
	$$('*[data-tooltip]').each(function(tooltipMaker){
		tooltipMakerCount = tooltipMakerCount + 1
		var markerId = tooltipMaker.get('id')
		if(!markerId){
			markerId = 'tooltipMaker-'+tooltipMakerCount
			tooltipMaker.set('id',markerId)
		}
		//either a new tab tooltip or an onpage tooltip
		var toolTipData = tooltipMaker.get('data-tooltip')
		if(toolTipData.substr(0,4) == 'url:'){
			var url = toolTipData.substr(4)
			tooltipMaker.addEvent('click',function(){
				tp.relocate(url,null,'tab')
			})
			
		}else{	
			var tooltip = new Element('div',{html:toolTipData,class:'tooltip',id:'tooltip-'+markerId})
			
			tooltipMaker.addEvent('click',function(e){
				tooltip.show()
				tooltip.inject(tooltipMaker,'after')
				tooltip.position({
					relativeTo:tooltipMaker,
					position:'bottomRight',
					edge:'upperLeft'
				})
				tooltip.addEvent('click',function(e){
					e.stopPropagation()//don't prevent highlighting, just stop propogation
				})
				e.stop()
			})
		}
	})
	window.addEvent('click',function(){
		$$('.tooltip').hide()
	})
//+	}
	
	//handle newTab anchor tabs
	$$('a.newTab').addEvent('click',function(event){
		tp.relocate(event.target.get('href'),null,'tab')
		return false
	})
	
});

