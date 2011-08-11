
dojo.require("hhreg.util");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.Button");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.Pagination");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojo.string");
dojo.require("dojo.date.locale");

dojo.provide("hhreg.admin.widget.EventsGrid");

dojo.declare("hhreg.admin.widget.EventsGrid", [dijit._Widget, dijit._Templated], {
	storeUrl: hhreg.util.contextUrl("/admin/dashboard/Events?a=listEvents"),
	baseClass: "hhreg-admin-EventsGrid",
	templateString: '<div><div data-dojo-attach-point="gridNode"></div></div>',
	postCreate: function() {
		this.inherited(arguments);
		
		var _this = this;
		
		var grid = new dojox.grid.EnhancedGrid({
			initialWidth: "100%",
			autoHeight: true,
			autoWidth: true,
			escapeHTMLInData: false,
			store: new dojo.data.ItemFileReadStore({url: _this.storeUrl, hierarchical: false, clearOnClose: true}),
			query: {eventId: "*"},
			plugins: {
				pagination: {}
			},
			structure: [
			    {field: "title", name: "Title", width: "100%"},
			    {field: "code", name: "Code", width: "100%"},
			    {name: "Status", width: "100%", get: function(rowIndex, storeItem) {
			    	if(storeItem) {
			    		var open = dojo.date.locale.parse(
		    				grid.store.getValue(storeItem, "regOpen"), 
		    				{datePattern: "yyyy-MM-dd", timePattern: "HH:mm:ss"}
			    		);
			    		var closed = dojo.date.locale.parse(
			    			grid.store.getValue(storeItem, "regClosed"),
			    			{datePattern: "yyyy-MM-dd", timePattern: "HH:mm:ss"}
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
			    	}
			    }},
			    {field: "regOpen", name: "Registration Open", width: "100%"},
			    {field: "regClosed", name: "Registration Closed", width: "100%"},
			    {name: "Options", width: "100%", get: function(rowIndex, storeItem) {
			    	return dojo.string.substitute(
			    		'<a href="${url}?id=${eventId}">Manage</a>', 
			    		{
			    			url: hhreg.util.contextUrl("/admin/event/Manage"), 
			    			eventId: grid.store.getValue(storeItem, "eventId")
			    		}
			    	);
			    }}
			],
			canSort: function(columnIndex) {
				return Math.abs(columnIndex) != 3; // can't sort on status column.
			}
		}, _this.gridNode);
		
		grid.startup();
		
		_this.gridNode = grid.domNode;
	}
});