dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.regOptionGroups");
	
	var setMultipleAllowedClass = function(value) {
		var minInput = dojo.byId("minimum");
		var maxInput = dojo.byId("maximum");
		var requiredInput = dojo.byId("required_true");
		
		if(value) {
			if(requiredInput.checked) {
				minInput.value = "1";
				maxInput.value = "1";
			}
			else {
				minInput.value = "0";
				maxInput.value = "0";
			}
			
			dojo.query(".multiple-allowed").removeClass("hide");
		}
		else {
			dojo.query(".multiple-allowed").addClass("hide");
		}
	};
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-reg-option-groups").forEach(function(item) {
			hhreg.xhrAddList.bind(item);

			// show/hide min & max.
			var multipleCheckbox = dojo.byId("allow-multiple");
			var label = dojo.query("label", item).filter(function(item) {
				return dojo.attr(item, "for") === multipleCheckbox.id;
			})[0];
			
			dojo.connect(multipleCheckbox, "onclick", function() {
				setMultipleAllowedClass(multipleCheckbox.checked);
			});
			
			dojo.connect(label, "onclick", function() {
				setMultipleAllowedClass(multipleCheckbox.checked);
			});
		});
	});
})();