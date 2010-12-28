
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
})();