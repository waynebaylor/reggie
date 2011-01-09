
(function() {
	dojo.provide("hhreg.reg.paymentTypes");
	
	var radioValueToFormClass = {
		1: ".check-payment-instructions",
		2: ".po-payment-instructions",
		3: ".authorizeNet-payment-instructions"
	};
	
	var hideForms = function() {
		for(var i in radioValueToFormClass) {
			dojo.query(radioValueToFormClass[i]).addClass("hide");
		}
	};
	
	var handleChange = function() {
		// remove any validation errors if they change payment type.
		hhreg.validation.removeMessages();
		dojo.query(".validation-icon").addClass("hide");
		
		dojo.query(".payment-type-tab").forEach(function(tab) {
			dojo.query("input[name=paymentType]", tab).forEach(function(radio) {
				if(radio.checked) {
					hideForms();
					
					dojo.addClass(tab, "selected-tab");
					
					dojo.query(radioValueToFormClass[radio.value])
						.removeClass("hide");
				}
				else {
					dojo.removeClass(tab, "selected-tab");
				}
			});
		});
	};
	
	////////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".payment-type-tab input[type=radio]").forEach(function(radio) {
			dojo.connect(radio, "onclick", function(event) {
				handleChange();
			});
			
			dojo.connect(radio, "onblur", function(event) {
				handleChange();
			});
		});
	});
})();