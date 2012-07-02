dojo.require("hhreg.xhrAddList");
dojo.require("hhreg.dialog");
dojo.require("hhreg.xhrTableForm");
dojo.require("hhreg.util");
dojo.require("dijit.layout.TabContainer");
dojo.require("dojox.layout.ContentPane");
dojo.require("dojo.hash");

(function() {
	dojo.provide("hhreg.admin.editRegistrations");
	
	var updatePaymentSummary = function() {
		var groupId = dojo.query("input[name=regGroupId]")[0].value;
		var eventId = dojo.query("input[name=eventId]")[0].value;
		
		var get = dojo.xhrGet({
			url: hhreg.util.contextUrl("/admin/registration/Registration?a=paymentSummary&eventId="+eventId+"&regGroupId="+groupId),
			handleAs: "text"
		});
		
		get.addCallback(function(response) {
			dojo.byId("payment-summary").innerHTML = response;
		});
	};
	
	function getTabChild(node) {
	    var tabTitle = dojo.query(".tab-label", node)[0].innerHTML;
	    
	    var subTabs = dojo.query(".registrant-sub-tab", node);
	    
	    if(subTabs.length == 0) {
	        return new dojox.layout.ContentPane({
	        	id: "tab-"+node.id,
	            title: tabTitle,
	            content: node
	        });
	    }
	    else {
	        var subTabc = dojo.place("<div></div>", dojo.body());
	        var subTabContainer = new dijit.layout.TabContainer({
	        	id: "tab-"+node.id,
	            title: tabTitle,
	            style: "width: 100%; height: 100%;",
	        	doLayout: false,
	        	nested: true
	        }, subTabc);
	        
	        subTabs.forEach(function(subTabNode) {
	            var subTabTitle = dojo.query(".sub-tab-label", subTabNode)[0].innerHTML;
	            
	            subTabContainer.addChild(new dojox.layout.ContentPane({
	            	id: "subtab-"+subTabNode.id,
	                title: subTabTitle,
	                content: subTabNode
	            }));
	        });
	        
	        return subTabContainer;
	    }
	}
	
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
		
		// print badge links
		dojo.query(".print-badge-link").forEach(function(item){
			var linksNode = hhreg.util.parentNode(item, ["registrant-links"]);
			var dialogNode = dojo.query(".print-badge-dialog", linksNode)[0];
			
			var dialog = hhreg.dialog.create({
				title: "Print Badge",
				trigger: item,
				content: dialogNode
			});
			
			var button = dojo.query(".print-badge-button", dialog.domNode)[0];
			var form = button.form;
			new dijit.form.Button({
				label: button.value,
				onClick: function() {
					form.submit();
					dialog.hide();
				}
			}, button).startup();
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
		
		// setup tab display.
		var tabc = dojo.place("<div></div>", dojo.query(".registrant-tab")[0].parentNode);
		var tabContainer = new dijit.layout.TabContainer({
			style: "width: 100%; height: 100%;",
			doLayout: false
		}, tabc);

		dojo.query(".registrant-tab").forEach(function(r) {
		    var child = getTabChild(r);
		    
		    tabContainer.addChild(child);
		});

		tabContainer.startup();
		
		// check if we should focus on a certain tab.
		var hashObj = dojo.queryToObject(dojo.hash());
		var showTab = hashObj && hashObj['showTab'];
		if(showTab) {
			var selectedTab = dijit.byId("tab-"+showTab);
			tabContainer.selectChild(selectedTab);
			
			// check if we need to show a sub-tab.
			var showSubTab = hashObj['showSubTab'];
			if(showSubTab) {
				var selectedSubTab = dijit.byId("subtab-"+showSubTab);
				selectedTab.selectChild(selectedSubTab);
			}
		}
		
		// show balance due when user adds a new payment.
		dojo.query(".fragment-payments .xhr-add-form").forEach(function(node) {
			var addLink = dojo.query(".add-link", node)[0];
			var amountInput = dojo.query("input[name=amount]", node)[0];
			
			dojo.connect(addLink, "onclick", function() {
				amountInput.value = dojo.byId("payment-balance-due").innerHTML;
			});
		});
	});
})();