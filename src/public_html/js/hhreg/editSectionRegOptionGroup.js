dojo.require("hhreg.xhrTableForm");
dojo.require("hhreg.xhrAddForm");
dojo.require("hhreg.list");

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
		var form = dojo.query(".fragment-edit form")[0];
		hhreg.xhrTableForm.bind(form);
		
		var addForm = dojo.query(".fragment-add .xhr-add-form")[0];
		hhreg.xhrAddForm.bind(addForm, function(response) {
			hhreg.list.update(response);
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