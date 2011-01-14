dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.editRegistrations");
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-payments").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
		
		dojo.query(".fragment-reg-options").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
})();