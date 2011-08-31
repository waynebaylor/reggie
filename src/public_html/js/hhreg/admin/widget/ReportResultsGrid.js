
dojo.require("hhreg.util");
dojo.require("dojo.cache");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.Pagination");
dojo.require("dojo.data.ItemFileReadStore");

dojo.provide("hhreg.admin.widget.ReportResultsGrid");

dojo.declare("hhreg.admin.widget.ReportResultsGrid", [dijit._Widget, dijit._Templated], {
	storeUrl: hhreg.util.contextUrl("/admin/report/Results"),
	eventId: 0,
	reportId: 0,
	baseClass: "hhreg-admin-ReportResultsGrid",
	templateString: dojo.cache("hhreg.admin.widget", "templates/ReportResultsGrid.html"),
	postCreate: function() {
		var _this = this;
		
		_this.storeUrl = _this.storeUrl+"?"+dojo.objectToQuery({eventId: _this.eventId, reportId: _this.reportId});
		
		_this.setupGrid();
		_this.setupData();
	},
	setupGrid: function() {
		var _this = this;
		
		var grid = new dojox.grid.EnhancedGrid({
			initialWidth: "100%",
			autoHeight: true,
			autoWidth: true,
			escapeHTMLInData: false,
			store: new dojo.data.ItemFileReadStore({data: {"identifier":"index", "items":[]}, hierarchical: false, clearOnClose: true}),
			query: {index: "*"},
			plugins: {
				pagination: {}
			},
			structure: [{name: "_", width: "100%"}],
			get: function(rowIndex, storeItem) {
				if(!storeItem) { return; }
				
				// this method is called in the context of a Cell, so "this" refers to the cell.
				var columnIndex = this.index; 
				return grid.store.getValues(storeItem, "data")[columnIndex];
			}
		}, _this.gridNode);
		
		grid.startup();
		
		// manually show loading message until xhr is completed.
		grid.showMessage(grid.loadingMessage);

		_this.gridNode = grid.domNode;
	},
	setupData: function() {
		var _this = this;
		
		var dataRequest = dojo.xhrGet({
			url: _this.storeUrl,
			handleAs: "json"
		});
		
		dataRequest.addCallback(function(results) {
			var grid = dijit.byNode(_this.gridNode);
			
			var s = [];
			dojo.forEach(results.headings, function(heading) {
				s.push({name: heading, width: "100%"});
			});
			
			if(results.showGroupLinks) {
				s.push({
					name: "Options",
					width: "100%",
					get: function(rowIndex, storeItem) {
						if(!storeItem) { return; }
						
						return dojo.string.substitute('<a href="${detailsUrl}">Details</a> <a href="${summaryUrl}">Summary</a>', {
							detailsUrl: grid.store.getValue(storeItem, "detailsUrl"),
							summaryUrl: grid.store.getValue(storeItem, "summaryUrl")
						});
					}
				});
			}
			
			grid.setStructure(s);
			
			grid.store.close();
			grid.setStore(new dojo.data.ItemFileReadStore({data: results, hierarchical: false, clearOnClose: true}));
		});
	}
});