/*Tools not specific to site*/
//Used to capture global context for use within object context
gthis = (function(){return this;})();


tp.tool = function(){};

/*handles multiple windows
reconsider logic when windowName provided and no such window*/
tp.windows = {};
tp.relocate = function(loc,windowName,type){
	//call window relocate function if window exists and unclosed
	if(windowName && tp.windows[windowName] && !tp.windows[windowName].closed){
		tp.windows[windowName].tp.relocate(loc)
		return false;
	}
	if(windowName){
		if(type == 'window'){
			tp.windows[windowName] =  window.open(loc,windowName,newWindow);
		}else{
			tp.windows[windowName] =  window.open(loc,windowName);
		}
		return false;
	}
	if(type == 'tab'){
		window.open(loc);
		return false;
	}else if(type == 'window'){
		window.open(loc,null,newWindow);
		return false;
	}
	
	if(typeof(loc) == 'number'){
		if(loc == 0){
			window.location.reload(false);
		}else{
			history.go(loc);
		}
	}else if(!loc){
		window.location = window.location+'';
	}else{
		window.location = loc;
	}
	return false;
}


tp.setCookie = function(name, value, exp){
	var c = name + "=" +escape( value )+";path=/;";
	if(exp){
		var expire = new Date();
		expire.setTime( expire.getTime() + exp);
		c += "expires=" + expire.toGMTString();
	}
	document.cookie = c;
}
tp.readCookie = function(name) {
	var name = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	}
	return null;
}

//take event object and get position
tp.eventPosition = function(e){;
	if (!e.pageX){
		var scroll = tp.scrollPosition()
		return {left:e.clientX + scroll.left,top:e.clientY + scroll.top};
	}
	return {left:e.pageX,top:e.pageY};
}

//find top left corner of element
tp.findPosition = function(ele){
	var curleft = curtop = 0;
	if(ele.offsetParent){
		do{
			curleft += ele.offsetLeft;
			curtop += ele.offsetTop
		}while(ele = ele.offsetParent);
	}
	return [curleft,curtop];
}

//get the current scroll position of the web page
tp.scrollPosition = function(){
	if(!self.pageXOffset){
		var left = (document.body.scrollLeft || document.documentElement.scrollLeft);
		var top = (document.body.scrollTop || document.documentElement.scrollTop);
		return {left:left,top:top};
	}
	return {left:self.pageXOffset,top:self.pageYOffset};
}


//get query string request variable
tp.getRequestVar = function(name){
	url = window.location+'';
	if(/\?/.test(url)){
		var uriParts = url.split('?');
		regexName = name.replace(/(\[|\])/g,"\\$1");
		var regex = new RegExp('(^|&)'+regexName+'=(.*?)(&|$)','g');
		var match = regex.exec(uriParts[1]);
		if(match){
			return match[2];
		}
	}
	return false;
}

//check if value is in an array
tp.arr = function(){};
tp.arr.vInA = function(v,a){
	var i;
	for(i in a){
		if(a[i] == v){
			return true;
		}
	}
	return false;
}

//add element to end of array
tp.arr.add = function(v,a){
	var key = a.length;
	a[key] = v;
	return key;
}
tp.arr.rmV = function(v,a){
	var i
	for(i in a){
		if(a[i] == v){
			unset(a[i])
		}
	}
}

//only add if not pre-existing
tp.arr.addUnique = function(v,a){
	if(!tp.arr.vInA(v,a)){
		a[a.length] = v;
	}
}
//get the first available key in a sequential array
tp.arr.firstAvailableKey = function(a){
	for(var i = 0; i < a.length; i++){
		if(typeof(a[i]) == 'undefined'){
			return i;
		}
	}
	return a.length;
}
//insert on first available key in a sequential array.  Returns key.
tp.arr.inOnAvailable = function(v,a){
	var key = tp.arr.firstAvailableKey(a)
	a[key] = v;
	return key;
}

//get key of first existing element
tp.arr.firstTakenKey = function(a){
	for(var i = 0; i < a.length; i++){
		if(typeof(a[i]) != 'undefined'){
			return i;
		}
	}
	return false;
}

//get first existing element
tp.arr.getFirstTaken = function(a){
	var key = tp.arr.firstTakenKey(a)
	return a[key];
}

//When deleting array element, element still counts towards arrays length.  This function ignores those elements
tp.arr.countedLength = function(a){
	count = 0;
	for(var i in a){
		count += 1;
	}
	return count;
}

tp.obj = {};
//get first object element
tp.obj.firstV = function(obj){
	for(var i in obj){
		return obj[i];
	}
}
//get first object key
tp.obj.firstK = function(obj){
	for(var i in obj){
		return i;
	}
}

//set page focus to element
tp.viewFocus = function(ele){
	var offset = $(ele).offset();
	console.log(offset);
	if(offset){
		window.scroll(offset.left,offset.top);
	}
}


tp.getUrl = function(url){
	url = url != null ? url : window.location
	url = url+'';//convert to string for vars like window.location
	return url
}
tp.getUrlParts = function(url,decode){
	decode = decode != null ? decode : true;
	url = tp.getUrl(url)
	var retPairs = []
	var urlParts = url.split('?');
	if(urlParts[1]){
		var pairs = urlParts[1].split('&');
		if(pairs.length > 0){
			Object.each(pairs,function(pair,i){
				var retPair = pair.split('=');
				if(decode){
					retPair[0] = decodeURIComponent(retPair[0])
					retPair[1] = decodeURIComponent(retPair[1])
				}
				retPairs.push(retPair)
			})
		}
	}
	return {page:urlParts[0],pairs:retPairs}
}
tp.getUrlFromParts = function(parts,encode){
	encode = encode != null ? encode : true;
	url = parts.page
	if(parts.pairs.length > 0){
		var pairs = []
		Object.each(parts.pairs,function(pair){
			if(encode){
				pair[0] = encodeURIComponent(pair[0])
				pair[1] = encodeURIComponent(pair[1])
			}
			pairs.push(pair[0]+'='+pair[1])
		})
		query = pairs.join('&')
		url = url+'?'+query
	}
	return url
}

tp.urlQueryFilter = function(regex,url,decode){
	var parts = tp.getUrlParts(url,decode)
	
	if(!instanceOf(regex,RegExp)){
		if(instanceOf(regex,String)){
			regex = new RegExp('^'+regex.escapeRegExp()+'$','g');
		}else{
			//replace all
			regex = new RegExp('.*','g');
		}
	}
	if(parts.pairs.length > 0){
		var newPairs = []
		var foundPair = false
		Object.each(parts.pairs,function(pair){
			if(!pair[0].match(regex)){
				newPairs.push(pair)
			}
		})
		parts.pairs = newPairs
	}
	return tp.getUrlFromParts(parts,decode)
}


//for adding variables to URL query string
tp.appendUrl = function(name,value,url,replace){
	replace = replace != null ? replace : false;
	
	if(replace){
		url = tp.urlQueryRemoveKey(url,name)
		alert('replace')
	}
	var parts = tp.getUrlParts(url)
	parts.pairs.push([name,value])
	return tp.getUrlFromParts(parts)
}


tp.appendsUrl = function(pairs,url,replace){
	Object.each(pairs,function(pair){
		url = tp.appendUrl(pair[0],pair[1],url,replace);
	})
	return url;
}



//Count an objects properties
tp.count = function(object){
	var count = 0;
	for(var i in object){
		count++;
	}
	return count;
}

//find id in array of objects
tp.tool.findIdInObjArray = function(id,arr){
	for(i in arr){
		if(arr[i].id == id){
			return arr[i];
		}
	}
}

//Binary to decimal
tp.tool.bindec = function(bin){
	bin = (bin+'').split('').reverse();
	var dec = 0;
	for(var i = 0; i < bin.length; i++){
		if(bin[i] == 1){
			dec += Math.pow(2,i);
		}
	}
	return dec;
}


//Decimal to binary
tp.tool.decbin = function(dec){
	var bits = '';
	for(var into = dec; into >= 1; into = Math.floor(into / 2)){
		bits += into % 2;
	}
	var lastBit = Math.ceil(into);
	if(lastBit){
		bits += lastBit;
	}
	return bits.split('').reverse().join('');
}
//will render string according to php rules
tp.str = function(str){
	if(typeof(str) == 'number'){
		return str+'';
	}
	if(!str){
		return '';
	}
	return str;
}
tp.toInt = function(s){
	if(typeof(s) == 'string'){	
		s = s.replace(/[^0-9]+/g,' ');
		s = s.replace(/^ +/g,'');
		s = s.replace(/^0+/g,'');
		s = s.split(' ');
		num = parseInt(s[0]);
	}else{
		num = parseInt(s);
	}
	if(isNaN(num)){
		return 0;
	}
	return num;
}
tp.str.pad = function(str,len,padChar,type){
	if(!padChar){
		padChar = '0';
	}
	if(!type){
		type = 'left';
	}
	if(type == 'left'){
		while (str.length < len){
			str = padChar + str;
		}
	}else{
		while (str.length < len){
			str = str + padChar;
		}
	}
	return str;
}


//set words to upper case
tp.str.ucwords = function(string){
	if(string){
		string = string.split(' ');
		var newString = Array();
		var i = 0;
		$.each(string,function(){
			newString[newString.length] = this.substr(0,1).toUpperCase()+this.substr(1,this.length)
		});
		return newString.join(' ');
	}
}

//capitalize the string
tp.str.capitalize = function(string){
	return string.charAt(0).toUpperCase() + string.slice(1);
}