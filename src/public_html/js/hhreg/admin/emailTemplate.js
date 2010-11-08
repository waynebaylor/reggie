
(function() {
	dojo.provide("hhreg.admin.emailTemplate");
	
	var handleChange = function() {
		var enabledTrue = dojo.byId("enabled_true");
		var enabledFalse = dojo.byId("enabled_false");
	
		if(enabledTrue.checked) {
			dojo.query("tr.template-info").removeClass("hide");
			dojo.removeClass(dojo.byId("email-test"), "hide");
		}
		else {
			dojo.query("tr.template-info").addClass("hide");
			dojo.addClass(dojo.byId("email-test"), "hide");
		}
	};
	
	////////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
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