dojo.require("hhreg.xhrAddList");
dojo.require("hhreg.xhrEditForm");

(function() {
	dojo.provide("hhreg.editSectionRegOptionGroup");
	
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
			minInput.value = "0";
			maxInput.value = "0";
		}
	};
	
	///////////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-options").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
		
		// connect the multiple checkbox behavior.
		var multipleCheckbox = dojo.byId("allow-multiple");		
		var label = dojo.query("label").filter(function(item) {
			return dojo.attr(item, "for") === multipleCheckbox.id;
		})[0];
		
		dojo.connect(multipleCheckbox, "onclick", function() {
			setMultipleAllowedClass(multipleCheckbox.checked);
		});
		
		dojo.connect(label, "onclick", function() {
			setMultipleAllowedClass(multipleCheckbox.checked);
		});
	});
})();