dojo.require("hhreg.xhrAddList");
dojo.require("hhreg.dialog");
dojo.require("hhreg.xhrTableForm");

(function() {
	dojo.provide("hhreg.admin.editRegistrations");
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-payments").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
		
		dojo.query(".fragment-reg-options").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
		
		// cancel registrant links
		dojo.query(".cancel-registrant").connect("onclick", function(event) {
			if(!confirm("Are you sure?")) {
				dojo.stopEvent(event);
			}
		});
		
		// cancel reg option links
		dojo.query(".cancel-reg-option-link").connect("onclick", function(event) {
			if(!confirm("Are you sure?")) {
				dojo.stopEvent(event);
			}	
		});
		
		// change reg type.
		dojo.query(".change-reg-type").forEach(function(item) {
			var content = dojo.query(".change-reg-type-content", item)[0];
			var form = dojo.query("form", content)[0];
			var triggerLink = dojo.query(".change-reg-type-link", item)[0];
			var redirectUrl = dojo.query(".change-reg-type-redirect", item)[0].value;
			
			var dialog = hhreg.dialog.create({
				title: "Change Registration Type",
				trigger: triggerLink,
				content: content,
				onClose: function() {
					hhreg.xhrTableForm.hideIcons(form)
				}
			});
			
			hhreg.xhrTableForm.bind(form, function() { 
				dialog.hide();
				document.location = hhreg.contextUrl(redirectUrl);
			});
		});
	});
})();