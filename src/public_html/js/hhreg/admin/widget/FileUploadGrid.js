
dojo.require("dojo.cache");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojo.io.iframe");
dojo.require("dojox.form.BusyButton");

dojo.provide("hhreg.admin.widget.FileUploadGrid");

dojo.declare("hhreg.admin.widget.FileUploadGrid", [dijit._Widget, dijit._Templated], {
	uploadAction: "saveFile",
	eventId: 0,
	baseClass: "hhreg-admin-FileUploadGrid",
	templateString: dojo.cache("hhreg.admin.widget", "templates/FileUploadGrid.html"),
	postCreate: function() {
		var _this = this;
		
		_this.setupButton();
		_this.setupGrid();
	},
	setupButton: function() {
		var _this = this;
		
		var b = new dojox.form.BusyButton({
			busyLabel: "Uploading...",
			label: "Upload",
			onClick: function() {
				dojo.io.iframe.send({
					form: _this.uploadFormNode,
					handleAs: "html",
					handle: function(response) {
						b.cancel();
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
			
		}, _this.gridNode);
		
		grid.startup();
		
		_this.gridNode = grid.domNode;
	}
});