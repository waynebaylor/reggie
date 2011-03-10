dojo.require("hhreg.xhrTableForm");
dojo.require("hhreg.util");

(function() {
	dojo.provide("hhreg.admin.login");
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-login form").forEach(function(item) { 
			hhreg.xhrTableForm.bind(item, function() {
				document.location = hhreg.util.contextUrl("/admin/dashboard/MainMenu?a=view");
			});
		});
		
		document.getElementsByName("email")[0].focus();
	});
})();