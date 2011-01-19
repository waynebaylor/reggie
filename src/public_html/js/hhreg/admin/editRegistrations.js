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
		
		// change reg type.
		var content = dojo.byId("change-reg-type-content");
		var form = dojo.query("form", content)[0];
		var dialog = hhreg.dialog.create({
			title: "Change Registration Type",
			trigger: dojo.byId("change-reg-type-link"),
			content: content,
			onClose: function() {
				hhreg.xhrTableForm.hideIcons(form)
			}
		});
		
		hhreg.xhrTableForm.bind(form, function() {
			dialog.hide();
			hhreg.xhrTableForm.hideIcons(form);
			document.location = hhreg.contextUrl(dojo.byId("change-reg-type-redirect").value);
		});
	});
})();