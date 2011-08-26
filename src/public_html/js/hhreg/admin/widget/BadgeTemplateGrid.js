
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
	setupCreateLink: function() {
		var _this = this;
		
		// prefix href with context.
		var href = dojo.attr(_this.createLinkNode, "href");
		dojo.attr(_this.createLinkNode, "href", hhreg.util.contextUrl(href));
	},
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
			    	
			    	var templateId = grid.store.getValue(storeItem, "id");
			    	
			    	var editUrl = hhreg.util.contextUrl("/admin/badge/EditBadgeTemplate?")+dojo.objectToQuery({eventId: _this.eventId, id: templateId});
			    	var copyUrl = hhreg.util.contextUrl("/admin/badge/BadgeTemplates?")+dojo.objectToQuery({eventId: _this.eventId, id: templateId});
			    	
			    	return dojo.string.substitute(
		    			'<a href="${editHref}">Edit</a> <a href="${copyHref}">Copy</a>', 
		    			{editHref: editUrl, copyHref: copyUrl}
		    		);
			    }}
			]
		}, _this.gridNode);
		
		grid.startup();

		_this.gridNode = grid.domNode;
	},
	setupDeleteButton: function() {
		var _this = this;
		
		var b = new dojox.form.BusyButton({
			label: "Delete Selected Templates",
			timeout: 60*1000,
			onClick: function() {
				var grid = dijit.byNode(_this.gridNode);
				var selectedItems = grid.selection.getSelected();

				// don't do anything if nothing is selected.
				if(!selectedItems || selectedItems.length == 0) { 
					b.cancel();
					return; 
				}
				
				// last chance to change your mind.
				if(!confirm("Are you sure?")) { 
					b.cancel();
					return; 
				}
				
				dojo.xhrPost({
					url: hhreg.util.contextUrl("/admin/badge/BadgeTemplates"),
					content: {
						a: "deleteTemplates",
						eventId: _this.eventId,
						"templateIds[]": dojo.map(selectedItems, function(storeItem) {
							return grid.store.getValue(storeItem, "id");
						})
					},
					handleAs: "html",
					handle: function(response) {
						b.cancel();
						
						grid.store.close();
						grid.setStore(new dojo.data.ItemFileReadStore({url: _this.storeUrl, hierarchical: false, clearOnClose: true}));
						grid.rowSelectCell.toggleAllSelection(false);
					}
				});
			}
		}, _this.deleteButtonNode);
		
		b.startup();
		
		_this.deleteButtonNode = b.domNode;
	}
});




