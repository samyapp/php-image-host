/*

PHP Image Host
www.phpace.com/php-image-host

Copyright (c) 2004,2008 Sam Yapp

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/


function si(url, w, h){
	w+= 40;
	h+= 40;
	sw = screen.width;
	sh = screen.height;
	sbars = 'no';
	if( w > sw ){
		w = sw;
		sbars = 'yes';
	}
	if( h > sh ){
		h = sh;
		sbars = 'yes';
	}
	l = (sw/2)-(w/2);
	t = (sh/2)-(h/2);
	window.open(url, '_blank', 'scrollbars='+sbars+',location=no,menubar=no,resizable=yes,width='+w+',height='+h+',left='+l+',top='+t);
	return false;
}

function exfname(from){
	if(from.indexOf('/') > -1){
		answer = from.substring(from.lastIndexOf('/'),from.length);
	}else{
		answer = from.substring(from.lastIndexOf('\\'),from.length);
	}
//	pos = answer.lastIndexOf('.');
//	if( pos > -1 ){
//		answer = answer.substring(0, pos);
//	}
	answer = answer.replace(/[^a-zA-Z0-9.]/, '');
	answer = answer.replace(/ /,'');
	return answer;
}


function gettitle(fname, ob){
	ob.value = fname;
}

function check(frm, toggle) { 
	var boxes = frm['ids[]'];
	if( typeof(boxes.length) == "undefined" || boxes == null){
		boxes.checked = toggle;
	}else{
		for (var i = 0; i < boxes.length; i++) { 
			boxes[i].checked = toggle;
		} 
	}
} 

function invert(frm){
	var boxes = frm['ids[]']; 
	if( typeof(boxes.length) == "undefined" || boxes == null){
		boxes.checked = !boxes.checked;
	}else{
		for (var i = 0; i < boxes.length; i++) { 
			boxes[i].checked = !boxes[i].checked;
		} 
	}
} 