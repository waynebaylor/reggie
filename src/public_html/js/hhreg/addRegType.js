dojo.require("hhreg.arrows");
dojo.require("hhreg.xhrLink");
dojo.require("hhreg.xhrAddForm");

(function() {
	dojo.provide("hhreg.addRegType");
	
	var update = function(content) {
		var admin = dojo.query("table.admin")[0];
		var parent = admin.parentNode;
		parent.removeChild(admin);
		parent.innerHTML = content;
		
		connectArrowHandlers();
		connectAddHandler();
		connectRemoveHandlers();
	};
	
	var connectArrowHandlers = function() {
		hhreg.arrows.bind({
			url: "/action/EditSection",
			upAction: "moveRegTypeUp",
			downAction: "moveRegTypeDown",
			callback: function(response) {
				update(response);
			}
		});
	};
	
	var connectRemoveHandlers = function() {
		dojo.query(".xhr-link").forEach(function(node) {
			hhreg.xhrLink.bind(node, function(response) {
				update(response);
			});
		});
	};
	
	var resetAddForm = function(node) {
		dojo.query("input[type=checkbox]", node).forEach(function(item) {
			item.checked = true;
		});
	};
	
	var connectAddHandler = function() {
		dojo.query(".xhr-add-form").forEach(function(node) {
			hhreg.xhrAddForm.bind(node, function(response) {
				resetAddForm(node);
				update(response);
			});
		});
	};
	
	///////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		connectArrowHandlers();
		connectRemoveHandlers();
		connectAddHandler();
	});
	
})();