
(function() {
	dojo.provide("hhreg.admin.editContactField");
	
	var connectFieldRestrictions = function(/*[.fragment-edit]*/ node) {
		var inputTypeSelect = dojo.query("select[name=formInputId]", node)[0];
		
		dojo.connect(inputTypeSelect, "onchange", function() {
			var selectedId = inputTypeSelect.options[inputTypeSelect.selectedIndex].value;
			dojo.query(".restriction", node).addClass("hide");
			
			// clear existing values when user changes type.
			dojo.query(".restriction", node).forEach(function(item) {
				dojo.query("input[type=text]", item).forEach(function(input) {
					input.value = "";
				});
				dojo.query("input[type=checkbox]", item).forEach(function(input) {
					input.checked = false;
				});
				dojo.query("input[type=radio]", item).forEach(function(input) {
					input.checked = false;
				});
				dojo.query("option", item).forEach(function(opt) {
					opt.selected = false;
				});
			});
			
			dojo.query(".formInput_"+selectedId, node).removeClass("hide");
			
			// hard-coded values for checkbox, radio, and select options
			if(dojo.indexOf(["3", "4", "5"], selectedId) >= 0) {
				dojo.query(".fragment-options").removeClass("hide");
			}
			else {
				dojo.query(".fragment-options").addClass("hide");
			}
		});
		
		var value = inputTypeSelect.options[inputTypeSelect.selectedIndex].value;
		dojo.query(".restriction", node).addClass("hide");
		dojo.query(".formInput_"+value, node).removeClass("hide");
		
		// hard-coded values for checkbox, radio, and select options
		if(dojo.indexOf(["3", "4", "5"], value) >= 0) {
			dojo.query(".fragment-options").removeClass("hide");
		}
		else {
			dojo.query(".fragment-options").addClass("hide");
		}
	};
	
	////////////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		var edit = dojo.query(".fragment-edit")[0];
		connectFieldRestrictions(edit);
	});
})();