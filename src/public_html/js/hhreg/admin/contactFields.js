dojo.require("hhreg.xhrAddList");

(function() {
	dojo.provide("hhreg.admin.contactFields");
	
	/**
	 * Show the right options in the add form.
	 */
	var connectFieldRestrictions = function(/*[.fragment-contact-fields]*/ node) {
		var inputTypeSelect = dojo.query("select[name=formInputId]", node)[0];
		
		dojo.connect(inputTypeSelect, "onchange", function() {
			var selectedId = inputTypeSelect.options[inputTypeSelect.selectedIndex].value;
			dojo.query(".restriction", node).addClass("hide");
			dojo.query(".formInput_"+selectedId, node).removeClass("hide");
		});
		
		var value = inputTypeSelect.options[inputTypeSelect.selectedIndex].value;
		dojo.query(".restriction", node).addClass("hide");
		dojo.query(".formInput_"+value, node).removeClass("hide");
	};
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-contact-fields").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
			
			connectFieldRestrictions(item);
		});
	});
})();