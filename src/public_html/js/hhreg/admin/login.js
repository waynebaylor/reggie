dojo.require("hhreg.xhrTableForm");

(function() {
	dojo.provide("hhreg.admin.login");
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-edit form").forEach(function(item) {
			hhreg.xhrTableForm.bind(item, function() {
				window.location.href = hhreg.contextUrl("/admin/MainMenu?a=view");
			});
		});
		
		document.getElementsByName("email")[0].focus();
	});
})();