dojo.require("hhreg.xhrTableForm");

(function() {
	dojo.provide("hhreg.editRegOptionPrice");
	
	///////////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		var form = dojo.query(".fragment-edit form")[0];
		hhreg.xhrTableForm.bind(form);
	});
})();