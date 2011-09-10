
dojo.require("hhreg.util");
dojo.require("dojo.cache");
dojo.require("dojo.string");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.Pagination");
dojo.require("dojo.data.ItemFileReadStore");

dojo.provide("hhreg.admin.widget.SearchResultsGrid");

dojo.declare("hhreg.admin.widget.SearchResultsGrid", [dijit._Widget, dijit._Templated], {
	storeUrl: hhreg.util.contextUrl("/admin/search/Search"),
	eventId: 0,
	searchTerm: "",
	baseClass: "hhreg-admin-SearchResultsGrid",
	templateString: '<div><div data-dojo-attach-point="gridNode"></div></div>',
	postCreate: function() {
		var _this = this;
		
		_this.storeUrl = _this.storeUrl+"?"+dojo.objectToQuery({a: "listResults", eventId: _this.eventId, searchTerm: _this.searchTerm});
		
		var grid = new dojox.grid.EnhancedGrid({
			initialWidth: "100%",
			autoHeight: true,
			autoWidth: true,
			escapeHTMLInData: false,
			store: new dojo.data.ItemFileReadStore({url: _this.storeUrl, hierarchical: false, clearOnClose: true}),
			query: {id: "*"},
			plugins: {
				pagination: {}
			},
			structure: [
			    {field: "fieldName", name: "Field", width: "100%"},
			    {field: "fieldValue", name: "Value", width: "100%"},
			    {name: "Options", width: "100%", get: function(rowIndex, storeItem) {
			    	if(!storeItem) { return; }
			    	
			    	if(grid.store.getValue(storeItem, "showDetailsLink")) {
			    		return dojo.string.substitute(
			    			'<a href="${detailsUrl}">Details</a> <a href="${summaryUrl}">Summary</a>', 
			    			{
			    				detailsUrl: grid.store.getValue(storeItem, "detailsUrl"), 
			    				summaryUrl: grid.store.getValue(storeItem, "summaryUrl")
			    			}
				    	);	
			    	}
			    	else {
			    		return dojo.string.substitute('<a href="${summaryUrl}">Summary</a>', {summaryUrl: grid.store.getValue(storeItem, "summaryUrl")});
			    	}
			    }}
			]
		}, _this.gridNode);
		
		grid.startup();

		_this.gridNode = grid.domNode;
	}
});