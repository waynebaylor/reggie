dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.mainMenu");
	
	//////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-events").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
		
		dojo.query(".fragment-users").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
})();