
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

dojo.provide("hhreg.admin.widget.ReportGrid");

dojo.declare("hhreg.admin.widget.ReportGrid", [dijit._Widget, dijit._Templated], {
	storeUrl: hhreg.util.contextUrl("/admin/report/ReportList"),
	eventId: 0,
	showCreateLink: true,
	showDeleteButton: true,
	baseClass: "hhreg-admin-ReportGrid",
	templateString: dojo.cache("hhreg.admin.widget", "templates/ReportGrid.html"),
	postCreate: function() {
		var _this = this;
		
		_this.storeUrl = _this.storeUrl+"?"+dojo.objectToQuery({a: "listReports", eventId: _this.eventId});
		
		_this.setupCreateLink();
		_this.setupGrid();
		_this.setupDeleteButton();
	},
	setupCreateLink: function() {
		var _this = this;
		
		if(_this.showCreateLink) {
			// add context info to the create page link.
			var createUrl = hhreg.util.contextUrl(dojo.attr(_this.createLinkNode, "href"));
			dojo.attr(_this.createLinkNode, "href", createUrl);
		}
		else {
			dojo.query(_this.createLinkNode).orphan();
		}
	},
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
			    {field: "name", name: "Name", width: "100%", get: function(rowIndex, storeItem) {
			    	if(!storeItem) { return; }
			    	
			    	var name = grid.store.getValue(storeItem, "name");
			    	var htmlUrl = grid.store.getValue(storeItem, "htmlResultsUrl");
			    	return dojo.string.substitute('<a href="${html}">${name}</a>', {name: name, html: htmlUrl});
			    }},
			    {name: "Export", width: "100%", get: function(rowIndex, storeItem) {
			    	if(!storeItem) { return; }
			    	
			    	var csvUrl = grid.store.getValue(storeItem, "csvResultsUrl");
			    	return dojo.string.substitute('<a target="_blank" href="${csv}">csv</a>', {csv: csvUrl});
			    }},
			    {name: "Options", width: "100%", get: function(rowIndex, storeItem) {
			    	if(!storeItem) { return; }
			    	
			    	var reportId = grid.store.getValue(storeItem, "id");
			    	var editUrl = hhreg.util.contextUrl("/admin/report/EditReport?")+dojo.objectToQuery({eventId: _this.eventId, reportId: reportId});
			    	return dojo.string.substitute('<a href="${editUrl}">Edit</a>', {editUrl: editUrl});
			    }}
			]
		}, _this.gridNode);
		
		grid.startup();

		_this.gridNode = grid.domNode;
	},
	setupDeleteButton: function() {
		var _this = this;
		
		if(!_this.showDeleteButton) {
			dojo.query(_this.deleteButtonNode).orphan();
			return;
		}
		
		var b = new dojox.form.BusyButton({
			label: "Delete Selected Reports",
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
					url: hhreg.util.contextUrl("/admin/report/ReportList"),
					content: {
						a: "deleteReports",
						eventId: _this.eventId,
						"reportIds[]": dojo.map(selectedItems, function(storeItem) {
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