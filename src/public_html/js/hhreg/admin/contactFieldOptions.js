dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.contactFieldOptions");
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-options").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
})();