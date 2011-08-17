
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojo.string");
dojo.require("dijit.layout.TabContainer");
dojo.require("dijit.layout.ContentPane");
dojo.require("dojox.layout.ContentPane");
dojo.require("hhreg.admin.data");
dojo.require("hhreg.util");

dojo.provide("hhreg.admin.widget.EventTabs");

dojo.declare("hhreg.admin.widget.EventTabs", [dijit._Widget, dijit._Templated], {
	user: {},
	eventId: 0,
	baseClass: "hhreg-admin-EventTabs",
	templateString: '<div><div data-dojo-attach-point="tabsNode"></div></div>',
	postCreate: function() {
		var _this = this;
		
		_this.setupTabs();
	},
	setupTabs: function() {
		var _this = this;
		
		var tabs = new dijit.layout.TabContainer({
			style: "width: 100%",
			doLayout: false
		}, _this.tabsNode);
		
		_this.setupRegFormTab(tabs)
		_this.setupReportsTab(tabs);
		_this.setupBadgeTab(tabs);
		_this.setupFilesTab(tabs);
		_this.setupPagesTab(tabs);
		
		tabs.startup();
		
		_this.tabsNode = tabs.domNode;
	},
	setupRegFormTab: function(tabContainer) {
		var _this = this;
		
		var showTab = hhreg.admin.data.role.userHasRole(_this.user, [
		    hhreg.admin.data.role.SYSTEM_ADMIN,
		    hhreg.admin.data.role.EVENT_ADMIN
		]);
		
		showTab = showTab || hhreg.admin.data.role.userHasRoleForEvent(_this.user, [
		    hhreg.admin.data.role.EVENT_MANAGER,
		    hhreg.admin.data.role.EVENT_REGISTRAR
	    ], _this.eventId);
		
		var content;
		if(showTab) {
			content = new dijit.layout.ContentPane({
				title: "Registration Form",
				content: "reg form"
				//href: hhreg.util.contextUrl("/admin/event/EditEvent?")+dojo.objectToQuery({id: _this.eventId})
			}, dojo.place("<div></div>", dojo.body()));
			
			content.startup();
			
			tabContainer.addChild(content);
		}
	},
	setupReportsTab: function(tabContainer) {},
	setupBadgeTab: function(tabContainer) {},
	setupFilesTab: function(tabContainer) {
		var _this = this;
		
		var showTab = hhreg.admin.data.role.userHasRole(_this.user, [
 		    hhreg.admin.data.role.SYSTEM_ADMIN,
 		    hhreg.admin.data.role.EVENT_ADMIN
 		]);
 		
 		showTab = showTab || hhreg.admin.data.role.userHasRoleForEvent(_this.user, [
 		    hhreg.admin.data.role.EVENT_MANAGER
 	    ], _this.eventId);
 		
 		var content;
 		if(showTab) {
 			content = new dojox.layout.ContentPane({
 				title: "Files",
 				href: hhreg.util.contextUrl("/admin/fileUpload/FileUpload?")+dojo.objectToQuery({id: _this.eventId})
 			}, dojo.place("<div></div>", dojo.body()));
 			
 			content.startup();
 			
 			tabContainer.addChild(content);
 		}
	},
	setupPagesTab: function(tabContainer) {}
});