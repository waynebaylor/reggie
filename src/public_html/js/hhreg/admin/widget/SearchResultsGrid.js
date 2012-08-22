
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
	metadataFields: [],
	baseClass: "hhreg-admin-SearchResultsGrid",
	templateString: '<div><div data-dojo-attach-point="gridNode"></div></div>',
	postCreate: function() {
		var _this = this;
		
		_this.storeUrl = _this.storeUrl+"?"+dojo.objectToQuery({a: "listResults", eventId: _this.eventId, searchTerm: _this.searchTerm});
		
		var gridStructure = [
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
		];
		
		// add metadata columns.
		dojo.forEach(_this.metadataFields, function(mf) {
			if(mf.metadataField == 'FIRST_NAME') {
				gridStructure.unshift({field: "firstName", name: mf.displayName, width: "100%"});
			}
			else if(mf.metadataField == 'LAST_NAME') {
				gridStructure.unshift({field: "lastName", name: mf.displayName, width: "100%"});
			}
			else if(mf.metadataField == 'EMAIL') {
				gridStructure.unshift({field: "email", name: mf.displayName, width: "100%"});
			}
		});
		
		gridStructure.unshift({field: "dateCancelled", name: "Date Cancelled", width: "100%"});
		gridStructure.unshift({field: "dateRegistered", name: "Date Registered", width: "100%"});
		
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
			structure: gridStructure
		}, _this.gridNode);
		
		grid.startup();

		_this.gridNode = grid.domNode;
	}
});