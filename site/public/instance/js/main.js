deemit = {}
deemit.makeMater = function(originalElement,signed,value){
	if(value === undefined){
		value = originalElement.get('text')
	}
	if(signed){
		if(value < 0){
			var type = 'against'
		}else{
			var type = 'for'
		}
	}else{
		var type = 'neutral'
	}
	var meterType = originalElement.get('data-type')+'-'+originalElement.get('class')
	
	var meter = Elements.from('<div class="meter meter-'+meterType+'">\
			<div class="measure '+type+'" title="'+value+'"></div>\
		</div>')
	meter.inject(originalElement,'after')
	meter.getChildren()[0].set('styles',{width:(value*10).abs()+'%'})
	originalElement.destroy()
	return meter;
}
deemit.makeDeviationMeter = function(significanceElement,mean,deviation){
	var value = significanceElement.get('text')
	var tmp = value - mean
	reverse = 1
	if(tmp < 0){
		reverse = -1
		tmp = tmp * reverse
	}
	if(deviation){
		var deviations = Math.sqrt(tmp/deviation) * reverse
	}else{
		var deviations = 0
	}
	var meterValue = Math.min(Math.max(deviations + 5,0),10)
	return deemit.makeMater(significanceElement,false,meterValue)
}
window.addEvent('domready', function(){
//+	add the various meters {
	$$('.controlFactor').each(function(factorElement){
		deemit.makeMater(factorElement)
	})
	
	$$('.forFactor').each(function(factorElement){
		deemit.makeMater(factorElement,true)
	})
	
	if(tp.json.significance){
		var significance = tp.json.significance;
		['user','entity','entity_relation'].each(function(type){
			if(significance[type].mean){
				var mean = significance[type].mean
				var deviation = significance[type].deviation
				$$('.significance[data-type="'+type+'"]').each(function(significanceElement){
					deemit.makeDeviationMeter(significanceElement,mean,deviation)
				})
			}
		})
	}
	if(tp.json.commentStats){
		var stats = tp.json.commentStats;
		['significance','enjoyment'].each(function(type){
			if(stats.significance.mean){
				$$('.commentStat[data-type="'+type+'"]').each(function(statElement){
					var meter = deemit.makeDeviationMeter(statElement,stats[type].mean,stats[type].deviation)
					meter.set('title',type)
				})
			}
		})
	}
//+	}
//+	format time {
	$$('.time').each(function(timeElement){
		var time = timeElement.get('text')
		var date = new Date(time * 1000)
		timeElement.set('title',date.format('rfc2822'))
		timeElement.set('text',date.timeDiff())
	})
//+	}
})