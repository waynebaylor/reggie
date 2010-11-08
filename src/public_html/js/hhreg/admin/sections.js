dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.sections");
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-sections").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
})();