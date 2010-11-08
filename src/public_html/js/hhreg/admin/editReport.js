dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.reports");
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-fields").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
})();