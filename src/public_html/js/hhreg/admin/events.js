dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.events");
	
	//////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-events").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
})();