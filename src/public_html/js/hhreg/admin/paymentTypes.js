
(function() {
	dojo.provide("hhreg.admin.paymentTypes");
	
	var showPaymentType = function(type) {
		dojo.query("tr.payment-type-details", type).removeClass("hide");
	};
	
	var hidePaymentType = function(type) {
		dojo.query("tr.payment-type-details", type).addClass("hide");
	};
	
	var handleChange = function(type, radio) {
		if(radio.value === "T" && radio.checked) {
			showPaymentType(type);
		}
		else {
			hidePaymentType(type);
		}
	};
	
	/////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".payment-type").forEach(function(type) {
			dojo.query("input[type=radio]", type).filter(function(item) {
				return item.name.match(/_enabled$/)
			}).forEach(function(radio) {
				dojo.connect(radio, "onclick", function(event) {
					handleChange(type, radio);
				});
			
				dojo.connect(radio, "onblur", function(event) {
					handleChange(type, radio);
				});
			});
		});
	});
})();