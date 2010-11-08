dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.variableQuantityOptions");
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-variable-quantity-options").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
})();