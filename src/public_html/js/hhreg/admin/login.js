dojo.require("hhreg.xhrTableForm");
dojo.require("hhreg.util");

(function() {
	dojo.provide("hhreg.admin.login");
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-edit form").forEach(function(item) {
			hhreg.xhrTableForm.bind(item, function() {
				document.location = hhreg.util.contextUrl("/admin/MainMenu?a=view");
			});
		});
		
		document.getElementsByName("email")[0].focus();
	});
})();