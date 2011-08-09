
dojo.require("hhreg.util");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.Button");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.IndirectSelection");
dojo.require("dojox.grid.enhanced.plugins.Pagination");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojox.lang.functional.fold");
dojo.require("dojo.string");

dojo.provide("hhreg.admin.widget.UsersGrid");

dojo.declare("hhreg.admin.widget.UsersGrid", [dijit._Widget, dijit._Templated], {
	storeUrl: hhreg.util.contextUrl("/admin/dashboard/Users?a=listUsers"),
	baseClass: "hhreg-admin-UsersGrid",
	templateString: '<div><div data-dojo-attach-point="gridNode"></div><div class="sub-divider"></div><div data-dojo-attach-point="deleteButtonNode"></div></div>',
	postCreate: function() { 
		this.inherited(arguments);
		
		this.setupGrid();
		this.setupDeleteButton();
	},
	setupGrid: function() {
		var _this = this;
		
		var grid = new dojox.grid.EnhancedGrid({
			initialWidth: "100%",
			autoHeight: true,
			autoWidth: true,
			escapeHTMLInData: false,
			store: new dojo.data.ItemFileReadStore({url: _this.storeUrl, hierarchical: false, clearOnClose: true}),
			query: {userId: "*"},
			plugins: {
				indirectSelection: {
					headerSelector: true
				},
				pagination: {}
			},
			structure: [
			    {field: "email", name: "Email", width: "100%"},
			    {field: "roles", name: "Roles", width: "100%", get: function(rowIndex, storeItem) {
			    	if(storeItem) {
				    	var roles = grid.store.getValues(storeItem, "roles");
				    	return dojo.map(roles, function(r) {
				    		r.eventCode = r.eventCode? "("+r.eventCode+")" : "";
				    		return dojo.string.substitute("${eventCode} ${name}", r);
				    	}).join("<br>");
			    	}
			    }},
			    {field: "options", name: "Options", width: "100%", get: function(rowIndex, storeItem) {
			    	if(storeItem) {
				    	var userId = grid.store.getValue(storeItem, "userId");
				    	var editUrl = hhreg.util.contextUrl("/admin/user/EditUser?")+dojo.objectToQuery({id: userId});
				    	return dojo.string.substitute('<a href="${url}">Edit</a>', {url: editUrl});
			    	}
			    }}
			],
			canSort: function(columnIndex) {
				return Math.abs(columnIndex) == 2; // only email column.
			}
		}, _this.gridNode);
		
		grid.startup();
		
		_this.gridNode = grid.domNode;
	},
	setupDeleteButton: function() {
		var _this = this;
		
		var deleteButton = new dijit.form.Button({
			label: "Delete Selected Users",
			onClick: function() {
				if(!confirm("Are you sure?")) { return;}
				
				var grid = dijit.byNode(_this.gridNode);
				var selectedItems = grid.selection.getSelected();

				dojo.xhrPost({
					url: hhreg.util.contextUrl("/admin/dashboard/Users"),
					content: {
						a: "deleteUsers",
						"ids[]": dojo.map(selectedItems, function(storeItem) {
							return grid.store.getValue(storeItem, "userId");
						})
					},
					handleAs: "json",
					handle: function(response) {
						grid.store.close();
						grid.setStore(new dojo.data.ItemFileReadStore({url: _this.storeUrl, hierarchical: false, clearOnClose: true}), {userId: "*"});
						grid.rowSelectCell.toggleAllSelection(false);
					}
				});
			}
		}, _this.deleteButtonNode);
		
		deleteButton.startup();
		
		_this.deleteButtonNode = deleteButton.domNode;
	}
});