dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.regTypes");
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-reg-types").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
})();