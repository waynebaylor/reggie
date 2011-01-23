
(function() {
	dojo.provide("hhreg.reg.regOptionGroup");
	
	/**
	 * check the parent inputs for the given option.
	 */
	var checkParents = function(/*[.reg-option]*/ option) {
		var parent = parentNode(option, "reg-option");
		while(parent) {
			var input = dojo.query("> .radio-label input", parent)[0];
			input.checked = true;
			parent = parentNode(parent, "reg-option");
		}
	};
	
	/**
	 * for each input in the given group do the following:
	 * 1. if the input is checked, then make sure each child group has 
	 *    a checked input.
	 * 2. if the input is not checked, then uncheck all inputs in child groups.
	 */
	var checkChildren = function(/*[.reg-option-group]*/ group) {
		dojo.query("> .reg-options", group).query(".reg-option").forEach(function(option) {
			var input = dojo.query("input", option)[0];
			if(input.checked) {
				dojo.query(".reg-option-group", option).forEach(function(group) {
					checkGroup(group);
				});
			}
			else {
				dojo.query("input", option).forEach(function(item) {
					item.checked = false;
				});
			}
		});
	};
	
	var checkGroup = function(/*[.reg-option-group]*/ group) {
		var hasChecked = false;
		var options = dojo.query("> .reg-options", group).query(".reg-option");
		options.forEach(function(option) {
			if(!hasChecked) {
				hasChecked = dojo.query("input", option)[0].checked;
			}
		});
		
		if(!hasChecked) {
			dojo.query("input", options[0])[0].checked = true;
		}
	};
	
	var parentNode = function(node, className) {
		var parent = node.parentNode;
		while(parent) {
			if(dojo.hasClass(parent, className)) {
				return parent;
			}
			
			parent = parent.parentNode;
		}
		
		return null;
	};
	
	var process = function(input) {
		var option = parentNode(input, "reg-option");
		if(input.checked) {
			checkParents(option);
		}

		var group = parentNode(input, "reg-option-group");
		checkChildren(group);
	};
	
	/////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".reg-option-group input").forEach(function(item) {
			dojo.connect(item, "onclick", function() {
				process(item);
			});
			
			dojo.connect(item, "onblur", function() {
				process(item);
			});
		});
	});
})();