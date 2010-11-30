dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.groupRegistration");

	var handleChange = function() {
		var enabledTrue = dojo.byId("enabled_true");
		var enabledFalse = dojo.byId("enabled_false");
		
		if(enabledTrue.checked) {
			dojo.query("tr.group-reg-info").removeClass("hide");
		}
		else {
			dojo.query("tr.group-reg-info").addClass("hide");
		}
	};
	
	////////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-fields").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
		
		//
		// show/hide when enabled/disabled.
		//
		var enabledTrue = dojo.byId("enabled_true");
		var enabledFalse = dojo.byId("enabled_false");
		
		dojo.connect(enabledTrue, "onclick", function() {
			handleChange();
		});
		
		dojo.connect(enabledFalse, "onclick", function() {
			handleChange();
		});
		
		dojo.connect(enabledTrue, "onblur", function() {
			handleChange();
		});
		
		dojo.connect(enabledFalse, "onblur", function() {
			handleChange();
		});
		
		
	});
})();