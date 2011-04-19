dojo.require("hhreg.xhrAddList");
dojo.require("hhreg.dialog");
dojo.require("hhreg.xhrTableForm");
dojo.require("hhreg.util");

(function() {
	dojo.provide("hhreg.admin.editRegistrations");
	
	var updatePaymentSummary = function() {
		var groupId = dojo.query("input[name=regGroupId]")[0].value;
		
		var get = dojo.xhrGet({
			url: hhreg.util.contextUrl("/admin/registration/Registration?a=paymentSummary&groupId="+groupId),
			handleAs: "text"
		});
		
		get.addCallback(function(response) {
			dojo.byId("payment-summary").innerHTML = response;
		});
	};
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-edit form").forEach(function(item) {
			if(hhreg.util.parentNode(item, ["var-quantity-options"])) {
				hhreg.xhrTableForm.bind(item, function() {
					updatePaymentSummary();
				});
			}
			else {
				hhreg.xhrTableForm.bind(item);
			}
		});
		
		dojo.query(".fragment-payments").forEach(function(item) { 
			hhreg.xhrAddList.bind(item, function() { 
				updatePaymentSummary();
			});
		});
		
		dojo.query(".fragment-reg-options").forEach(function(item) {
			hhreg.xhrAddList.bind(item, function() {
				updatePaymentSummary();
				
				// re-bind confirmation.
				dojo.query(".cancel-reg-option-link").connect("onclick", function(event) {
					if(!confirm("Are you sure?")) {
						dojo.stopEvent(event);
					}	
				});
			});
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
		
		// delete registrant links
		dojo.query(".delete-registrant").connect("onclick", function(event) {
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
				document.location = hhreg.util.contextUrl(redirectUrl);
			});
		});
		
		// add registrant to group.
		dojo.query(".add-registrant").forEach(function(item) {
			var content = dojo.query(".add-registrant-content", item)[0];
			var form = dojo.query("form", content)[0];
			var triggerLink = dojo.query(".add-registrant-link", item)[0];
			
			var dialog = hhreg.dialog.create({
				title: 'Add Registrant To Group',
				trigger: triggerLink,
				content: content
			});
			
			hhreg.xhrTableForm.bind(form);
		});
	});
})();