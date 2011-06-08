dojo.require("hhreg.list");
dojo.require("hhreg.xhrAddForm");

(function() {
	var _addList = dojo.provide("hhreg.xhrAddList");
	
	_addList.bind = function(node, callback) {
		hhreg.list.bind(node, callback);
		
		dojo.query(".fragment-add .xhr-add-form", node).forEach(function(addForm) {
			hhreg.xhrAddForm.bind(addForm, function(response) {
				hhreg.list.update(response, node, callback);
				
				if(callback) {
					callback(response);
				}
			});
		});
	};
})();