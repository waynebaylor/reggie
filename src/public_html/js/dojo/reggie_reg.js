/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

/*
	This is an optimized version of Dojo, built for deployment and not for
	development. To get sources and documentation, please visit:

		http://dojotoolkit.org
*/

if(!dojo._hasResource["hhreg.util"]){dojo._hasResource["hhreg.util"]=true;(function(){var _1=dojo.provide("hhreg.util");_1.parentNode=function(_2,_3){var _4=_2.parentNode;var i;while(_4){for(i=0;i<_3.length;++i){if(dojo.hasClass(_4,_3[i])){return _4;}}_4=_4.parentNode;}return null;};_1.contextUrl=function(_5){var _6=dojo.byId("reggie.contextPath").value+_5;return _6.replace(/\/\//g,"/");};})();}if(!dojo._hasResource["hhreg.validation"]){dojo._hasResource["hhreg.validation"]=true;(function(){var _7=dojo.provide("hhreg.validation");var _8=function(_9){if(_9.id){var _a=dojo.query("label").filter(function(_b){return dojo.attr(_b,"for")===_9.id;});return _a[0];}return null;};var _c=function(_d,_e){dojo.style(_d,"position","static");_e.appendChild(_d);};var _f=function(div,_10){var _11=hhreg.util.parentNode(_10,["checkbox-label","radio-label"]);dojo.style(div,{position:"static",padding:"0px"});dojo.place(div,_11,"before");};var _12=function(div,_13){_14(div,hhreg.util.parentNode(_13,["hhreg-calendar"]));};var _14=function(div,_15){_15=_8(_15)||_15;position=dojo.position(_15,true);dojo.style(div,{top:position.y+"px",left:(position.x+position.w)+"px"});if(_15.form){_15.form.appendChild(div);}else{dojo.body().appendChild(div);}};var _16=function(_17,_18){var div=dojo.create("div");dojo.addClass(div,"error-message");var img=dojo.create("img",{src:hhreg.util.contextUrl("/images/caution_red.gif"),alt:"Validation Error",title:"Validation Error"});div.appendChild(img);var _19=dojo.create("span");dojo.addClass(_19,"error-text");_19.appendChild(document.createTextNode(" "+_18));div.appendChild(_19);var _1a;if(_17.id==="general-errors"){_c(div,_17);}else{if(hhreg.util.parentNode(_17,["hhreg-calendar"])){_12(div,_17);}else{if(hhreg.util.parentNode(_17,["checkbox-label","radio-label"])){_f(div,_17);}else{_14(div,_17);}}}};_7.removeMessages=function(_1b){if(_1b){dojo.query(".error-message",_1b).orphan();}else{dojo.query(".error-message").orphan();}};_7.showMessages=function(_1c,_1d){var _1e;var _1f;for(fieldName in _1c){if(fieldName==="general"){_1e=dojo.byId("general-errors");}else{if(_1d){for(var i=0;i<_1d.elements.length;++i){if(_1d.elements[i].name===fieldName){_1e=_1d.elements[i];break;}}}else{_1e=document.getElementsByName(fieldName)[0];}}_16(_1e,_1c[fieldName]);}};})();}
