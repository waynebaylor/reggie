
dojo.require("dojo.cache");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojo.io.iframe");
dojo.require("dojox.form.BusyButton");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.IndirectSelection");
dojo.require("dojox.grid.enhanced.plugins.Pagination");
dojo.require("dojo.data.ItemFileReadStore");

dojo.provide("hhreg.admin.widget.FileUploadGrid");

dojo.declare("hhreg.admin.widget.FileUploadGrid", [dijit._Widget, dijit._Templated], {
	storeUrl: hhreg.util.contextUrl("/admin/fileUpload/FileUpload"),
	uploadAction: hhreg.util.contextUrl("/admin/fileUpload/FileUpload"),
	actionMethod: "saveFile",
	eventId: 0,
	baseClass: "hhreg-admin-FileUploadGrid",
	templateString: dojo.cache("hhreg.admin.widget", "templates/FileUploadGrid.html"),
	postCreate: function() {
		var _this = this;
		
		_this.storeUrl = _this.storeUrl+"?"+dojo.objectToQuery({a: "listFiles", eventId: _this.eventId});
		
		_this.setupUploadButton();
		_this.setupGrid();
		_this.setupDeleteButton();
	},
	setupUploadButton: function() {
		var _this = this;
		
		var b = new dojox.form.BusyButton({
			busyLabel: "Uploading...",
			label: "Upload",
			timeout: 60*1000,
			onClick: function() {
				dojo.io.iframe.send({
					form: _this.uploadFormNode,
					handleAs: "html",
					handle: function(response) {
						b.cancel();
						
						var grid = dijit.byNode(_this.gridNode);
						grid.store.close();
						grid.setStore(new dojo.data.ItemFileReadStore({url: _this.storeUrl, hierarchical: false, clearOnClose: true}));
						grid.rowSelectCell.toggleAllSelection(false);
					}
				});
			}
		}, _this.uploadButtonNode);
		b.startup();
		
		_this.uploadButtonNode = b.domNode;
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
			    {field: "name", name: "Name", width: "100%"},
			    {field: "link", name: "Link", width: "100%", formatter: function(value) {
		    		return dojo.string.substitute('<a target="_blank" href="${href}">${label}</a>', {href: value, label: value});
			    }}
			]
		}, _this.gridNode);
		
		grid.startup();

		_this.gridNode = grid.domNode;
	},
	setupDeleteButton: function() {
		var _this = this;
		
		var b = new dojox.form.BusyButton({
			label: "Delete Selected Files",
			timeout: 60*1000,
			onClick: function() {
				if(!confirm("Are you sure?")) { return; }
				
				var grid = dijit.byNode(_this.gridNode);
				var selectedItems = grid.selection.getSelected();
				
				dojo.xhrPost({
					url: _this.uploadAction,
					content: {
						a: "deleteFiles",
						eventId: _this.eventId,
						"fileNames[]": dojo.map(selectedItems, function(storeItem) {
							return grid.store.getValue(storeItem, "name");
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