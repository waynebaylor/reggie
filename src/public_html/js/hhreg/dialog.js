dojo.require("dijit.Dialog");

(function() {
	var dialog = dojo.provide("hhreg.dialog");
	
	dialog.create = function(/*{title, trigger, content, onClose}*/ spec) {
		//
		// summary: 
		//          create a popup dialog.
		// spec:
		//          title: dialog title
		//          trigger: DOM Node to which the show dialog onclick event will be attached
		//          content: DOM Node containing the content of the dialog
		//          onClose: function to execute when user cancels dialog
		//
		
		var d = new dijit.Dialog({
			title: spec.title,
			content: spec.content,
			duration: 150
		});
		
		if(spec.onClose) {
			dojo.connect(d, "hide", function() {
				spec.onClose();
			});
		}
		
		// set up the dialog.
		dojo.body().appendChild(d.domNode);
		d.startup();
		dojo.removeClass(spec.content, "hide");
		
		// show dialog when user clicks link.
		dojo.connect(spec.trigger, "onclick", function() {
			d.show();
		});
		
		return d;
	};
})();