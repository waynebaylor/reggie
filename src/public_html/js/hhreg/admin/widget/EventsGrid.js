
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
dojo.require("dojo.date.locale");

dojo.provide("hhreg.admin.widget.EventsGrid");

dojo.declare("hhreg.admin.widget.EventsGrid", [dijit._Widget, dijit._Templated], {
	storeUrl: hhreg.util.contextUrl("/admin/dashboard/Events?a=listEvents"),
	baseClass: "hhreg-admin-EventsGrid",
	templateString: dojo.cache("hhreg.admin.widget", "templates/EventsGrid.html"),
	postCreate: function() {
		this.inherited(arguments);
		
		var _this = this;
		
		_this.setupGrid();
		_this.setupDeleteButton();
	},
	setupGrid: function() {
		var _this = this;
		
		var disabledRowIndexes = [];
		
		var grid = new dojox.grid.EnhancedGrid({
			initialWidth: "100%",
			autoHeight: true,
			autoWidth: true,
			escapeHTMLInData: false,
			store: new dojo.data.ItemFileReadStore({url: _this.storeUrl, hierarchical: false, clearOnClose: true}),
			query: {eventId: "*"},
			plugins: {
				indirectSelection: {},
				pagination: {}
			},
			structure: [
			    {field: "title", name: "Title", width: "100%"},
			    {field: "code", name: "Code", width: "100%"},
			    {name: "Status", width: "100%", get: function(rowIndex, storeItem) {
			    	if(!storeItem) { return; }
			    	
		    		var open = dojo.date.locale.parse(
	    				grid.store.getValue(storeItem, "regOpen"), 
	    				{datePattern: "yyyy-MM-dd", timePattern: "HH:mm"}
		    		);
		    		var closed = dojo.date.locale.parse(
		    			grid.store.getValue(storeItem, "regClosed"),
		    			{datePattern: "yyyy-MM-dd", timePattern: "HH:mm"}
		    		);
		    		
		    		var current = new Date();
		    		
		    		if(current > open && current < closed) {
		    			return "Active";
		    		}
		    		else if(current < open) {
		    			return "Upcoming";
		    		}
		    		else if(current > closed) {
		    			return '<span style="color:#aaa;">Inactive</span>';
		    		}
			    }},
			    {field: "regOpen", name: "Registration Open", width: "100%", formatter: function(value) {
			    	return value && value.replace(/00:00/, "");
			    }},
			    {field: "regClosed", name: "Registration Closed", width: "100%", formatter: function(value) {
		    		return value && value.replace(/00:00/, "");
			    }},
			    {field: "manageUrl", name: "Options", width: "100%", get: function(rowIndex, storeItem) {
			    	if(!storeItem) { return; }
			    		
			    	return dojo.string.substitute(
			    			'<a href="${url}">Manage</a>', 
			    			{url: grid.store.getValue(storeItem, "manageUrl")}
			    	);
			    }}
			],
			canSort: function(columnIndex) {
				return Math.abs(columnIndex) != 3 &&  	// can't sort on status column.
					   Math.abs(columnIndex) != 6;		// can't sort on options column.
			}
		}, _this.gridNode);
		
		grid.startup();
		
		// set the disabled state on the appropriate rows.
		grid.store.fetch({onItem: function(storeItem) {
	    	var canDelete = grid.store.getValue(storeItem, "canDelete", false);
	    	if(!canDelete) { 
	    		var rowIndex = grid.getItemIndex(storeItem);
				grid.rowSelectCell.setDisabled(rowIndex, true);
	    	}
		}});
		
		_this.gridNode = grid.domNode;
	},
	setupDeleteButton: function() {
		var _this = this;
		
		var b = new dojox.form.BusyButton({
			label: "Delete Selected Events",
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
				
				var eventIds = dojo.map(selectedItems, function(storeItem) {
					return grid.store.getValue(storeItem, "eventId");
				});
				window.location.href = hhreg.util.contextUrl("/admin/dashboard/ConfirmDeleteEvent?")+dojo.objectToQuery({"eventIds[]": eventIds});
			}
		}, _this.deleteButtonNode);
		
		b.startup();
		
		_this.deleteButtonNode = b.domNode;
	}
});