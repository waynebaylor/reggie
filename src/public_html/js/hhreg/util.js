dojo.require("dijit.form.Textarea");

(function() {
	var util = dojo.provide("hhreg.util");
	
	util.parentNode = function(/*DOM Node*/ node, /*Array*/ classNames) {
		//
		// summary: 
		//          find a node's ancestor with at least one of the given CSS classes. if 
		//          no ancestor is found, then null is returned.
		//
		// node:    
		//          the node whose ancestors will be examined.
		//
		// classNames:
		//          the CSS classes for which to look.
		//
		
		var parent = node.parentNode;
		
		var i;
		while(parent) {
			for(i=0; i<classNames.length; ++i) {
				if(dojo.hasClass(parent, classNames[i])) {
					return parent;
				}
			}
			
			parent = parent.parentNode;
		}
		
		return null;
	};
	
	util.contextUrl = function(/*String*/ url) {
		//
		// return the given url prefixed with the context path.
		//
		
		var newUrl = dojo.byId("reggie.contextPath").value + url;
		
		// replace double '/'s. this may happen if the context path is
		// simply '/' and the url starts with a '/'.
		return newUrl.replace(/\/\//g, "/");
	};
	
	util.enhanceTextarea = function(/*DOM Node*/ node) {
		var ta = new dijit.form.Textarea({
			name: node.name, 
			style: "min-height:75px; width:900px;"
		}, node);
		
		ta.startup();
	};
})();