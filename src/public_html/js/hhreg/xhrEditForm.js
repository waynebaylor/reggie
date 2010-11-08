dojo.require("hhreg.xhrTableForm");

(function() {
	dojo.provide("hhreg.xhrEditForm");
	
	///////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-edit form").forEach(function(item) {
			hhreg.xhrTableForm.bind(item);
		});
	});
})();