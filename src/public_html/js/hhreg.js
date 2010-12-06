
(function() {
	var hhreg = dojo.provide("hhreg");
	
	hhreg.contextUrl = function(/*String*/ url) {
		//
		// return the given url prefixed with the context path.
		//
		
		var newUrl = dojo.byId("reggie.contextPath").value + url;
		
		// replace double '/'s. this may happen if the context path is
		// simply '/' and the url starts with a '/'.
		return newUrl.replace(/\/\//g, "/");
	};
})();

