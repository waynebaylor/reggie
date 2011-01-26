dojo.require("hhreg.dialog");
dojo.require("hhreg.xhrTableForm");

(function() {
	dojo.provide("hhreg.admin.reportResults");
	
	////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		var triggerLink = dojo.byId("create-reg-link");
		var content = dojo.byId("create-reg-content");
		var form = dojo.query("form", content)[0];
		var redirectUrl = dojo.byId("create-reg-redirect").value;
		
		var dialog = hhreg.dialog.create({
			title: "Create New Registration",
			trigger: triggerLink,
			content: content,
			onClose: function() {
				hhreg.xhrTableForm.hideIcons(form)
			}
		});
		
		hhreg.xhrTableForm.bind(form, function() { 
			dialog.hide();
			hhreg.xhrTableForm.hideIcons(form);
			document.location = hhreg.contextUrl(redirectUrl);
		});
	});
})();