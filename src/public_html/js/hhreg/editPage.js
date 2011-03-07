dojo.require("hhreg.xhrAddForm");
dojo.require("hhreg.list");

(function() {
	dojo.provide("hhreg.editPage");
	
	//////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		var addForm = dojo.query(".fragment-add .xhr-add-form")[0];
		hhreg.xhrAddForm.bind(addForm, function(response) {
			hhreg.list.update(response);
		});
	});
})();