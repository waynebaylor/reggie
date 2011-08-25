
dojo.require("hhreg.util");
dojo.require("dojo.cache");
dojo.require("dojo.string");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojox.form.BusyButton");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.IndirectSelection");
dojo.require("dojox.grid.enhanced.plugins.Pagination");
dojo.require("dojo.data.ItemFileReadStore");

dojo.provide("hhreg.admin.widget.BadgeTemplateGrid");

dojo.declare("hhreg.admin.widget.BadgeTemplateGrid", [dijit._Widget, dijit._Templated], {
	storeUrl: hhreg.util.contextUrl("/admin/badge/BadgeTemplates"),
	eventId: 0,
	baseClass: "hhreg-admin-BadgeTemplateGrid",
	templateString: dojo.cache("hhreg.admin.widget", "templates/BadgeTemplateGrid.html"),
	postCreate: function() {
		var _this = this;
		
		_this.storeUrl = _this.storeUrl+"?"+dojo.objectToQuery({a: "listTemplates", eventId: _this.eventId});
		
		_this.setupCreateLink();
		_this.setupPrintLink();
		_this.setupGrid();
		_this.setupDeleteButton();
	},
	setupCreateLink: function() {},
	setupPrintLink: function() {},
	setupGrid: function() {
		var _this = this;
		
		var grid = new dojox.grid.EnhancedGrid({
			initialWidth: "100%",
			autoHeight: true,
			autoWidth: true,
			escapeHTMLInData: false,
			store: new dojo.data.ItemFileReadStore({url: _this.storeUrl, hierarchical: false, clearOnClose: true}),
			query: {id: "*"},
			plugins: {
				indirectSelection: {
					headerSelector: true
				},
				pagination: {}
			},
			structure: [
			    {field: "name", name: "Name", width: "100%"},
			    {field: "type", name: "Type", width: "100%"},
			    {field: "registrationTypes", name: "Registration Types", width: "100%", get: function(rowIndex, storeItem) {
			    	if(!storeItem) { return; }
			    	
			    	if(grid.store.getValue(storeItem, "appliesToAll")) {
			    		return "All";
			    	}
			    	
			    	var regTypes = grid.store.getValues(storeItem, "appliesTo");
			    	return dojo.map(regTypes, function(r) {
			    		return dojo.string.substitute("(${code}) ${description}", r);
			    	}).join("<br>");
			    }},
			    {name: "Options", width: "100%", get: function(rowIndex, storeItem) {
			    	if(!storeItem) { return; }
			    	
			    	// edit & copy links
			    }}
			]
		}, _this.gridNode);
		
		grid.startup();

		_this.gridNode = grid.domNode;
	},
	setupDeleteButton: function() {}
});