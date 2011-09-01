dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojox.form.BusyButton");
dojo.require("hhreg.util");

dojo.provide("hhreg.admin.widget.SearchForm");

dojo.declare("hhreg.admin.widget.SearchForm", [dijit._Widget, dijit._Templated], {
	eventId: 0,
	baseClass: "reggie-admin-SearchForm",
	templateString: dojo.cache("hhreg.admin.widget", "templates/SearchForm.html"),
	postCreate: function() {
		var _this = this;
		
		var contextAction = hhreg.util.contextUrl(dojo.attr(_this.formNode, "action"));
		dojo.attr(_this.formNode, "action", contextAction);
		
		var b = new dojox.form.BusyButton({
			label: "Find Registrations",
			busyLabel: "Searching...",
			onClick: function() {
				if(_this.formNode.searchTerm.value) {
					_this.formNode.submit();
				}
				else {
					b.cancel();
				}
			}
		}, _this.submitButtonNode);
		
		b.startup();
		
		_this.submitButtonNode = b.domNode;
	}
});