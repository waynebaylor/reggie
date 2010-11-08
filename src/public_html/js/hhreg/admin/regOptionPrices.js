dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.regOptionPrices");
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-reg-option-prices").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
})();