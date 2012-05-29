dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuBar");
dojo.require("dijit.MenuItem");
dojo.require("dijit.PopupMenuBarItem");
dojo.require("dijit.MenuBarItem");
dojo.require("dijit.TooltipDialog");
dojo.require("hhreg.admin.widget.SearchForm");

dojo.provide("hhreg.admin.widget.ActionMenuBar");

dojo.declare("hhreg.admin.widget.ActionMenuBar", [dijit._Widget, dijit._Templated], {
	showUsers: false,
	showEvents: false,
	showCreateEvent: false,
	showEventMenu: false,
	eventLabel: "",
	showReports: false,
	showRegForm: false,
	showBadgeTemplates: false,
	showFiles: false,
	showPages: false,
	showCreateReg: false,
	eventId: 0,
	baseClass: "reggie-admin-ActionMenuBar",
	templateString: dojo.cache("hhreg.admin.widget", "templates/ActionMenuBar.html"),
	postCreate: function() {
		this.inherited(arguments);
		
		this.setupGeneralMenu();
		this.setupEventMenu();
		this.setupUserMenu();
	},
	setupGeneralMenu: function() {
		var _this = this;
		
		var menu = new dijit.MenuBar({}, _this.generalMenuNode);
		
		var usersMenu;
		if(_this.showUsers) {
			usersMenu = new dijit.Menu({});
			usersMenu.addChild(new dijit.MenuItem({
				label: "Create User",
				onClick: function() {
					window.location.href = hhreg.util.contextUrl("/admin/user/CreateUser");
				}
			}));
			usersMenu.addChild(new dijit.MenuItem({
				label: "Manage Users",
				onClick: function() {
					window.location.href = hhreg.util.contextUrl("/admin/dashboard/Users");
				}
			}));
			
			menu.addChild(new dijit.PopupMenuBarItem({
				label: '<span class="general-item">Users</span>', 
				popup: usersMenu
			}));
		}
		
		var eventsMenu
		if(_this.showEvents) {
			eventsMenu = new dijit.Menu({});
			
			if(_this.showCreateEvent) {
				eventsMenu.addChild(new dijit.MenuItem({
					label: "Create Event",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl("/admin/event/CreateEvent");
					}
				}));
			}
			
			eventsMenu.addChild(new dijit.MenuItem({
				label: "Manage Events",
				onClick: function() {
					window.location.href = hhreg.util.contextUrl("/admin/dashboard/Events");
				}
			}));
			
			menu.addChild(new dijit.PopupMenuBarItem({
				label: '<span class="general-item">Events</span>', 
				popup: eventsMenu
			}));
		}

		menu.startup();
		
		_this.generalMenuNode = menu.domNode;
		
		if(!_this.showEventMenu) {
			dojo.style(_this.generalMenuNode.parentNode, "width", "100%");
		}
	},
	setupEventMenu: function() {
		var _this = this;
		
		if(_this.showEventMenu) {
			var eventMenu = new dijit.MenuBar({}, _this.eventMenuNode);
		
			if(_this.showCreateReg) {
				var eventActions = new dijit.Menu({});
				
				eventActions.addChild(new dijit.MenuItem({
					label: "Create Registration",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl("/admin/registration/CreateRegistration?")+dojo.objectToQuery({eventId: _this.eventId});
					}
				}));
				
				eventMenu.addChild(new dijit.PopupMenuBarItem({
					label: _this.eventLabel,
					style: {color: "darkorange", opacity: "1.0", fontWeight: "bold"},
					popup: eventActions
				}));
			}
			else {
				var eventLabelItem = new dijit.MenuBarItem({
					style: {color: "darkorange", opacity: "1.0", fontWeight: "bold"},
					disabled: false,
					label: _this.eventLabel,
					onClick: function() {}
				});
				dojo.query("*", eventLabelItem.domNode).style("opacity", "1.0");
				eventMenu.addChild(eventLabelItem);	
			}
			
			if(_this.showReports) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Reports",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl("/admin/report/ReportList?")+dojo.objectToQuery({eventId: _this.eventId}); 
					}
				}));
			}
			if(_this.showRegForm) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Registration Form",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl("/admin/event/EditEvent?")+dojo.objectToQuery({eventId: _this.eventId}); 
					}
				}));
			}
			if(_this.showBadgeTemplates) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Badge Templates",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl("/admin/badge/BadgeTemplates?")+dojo.objectToQuery({eventId: _this.eventId}); 
					}
				}));
			}
			if(_this.showFiles) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Files",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl("/admin/fileUpload/FileUpload?")+dojo.objectToQuery({eventId: _this.eventId}); 
					}
				}));
			}
			if(_this.showPages) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Pages",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl("/admin/staticPage/PageList?")+dojo.objectToQuery({eventId: _this.eventId}); 
					}
				}));
			}
			
			var searchForm = new hhreg.admin.widget.SearchForm({eventId: _this.eventId}, dojo.place("<div></div>", dojo.body()));
			searchForm.startup();
			
			eventMenu.addChild(new dijit.PopupMenuBarItem({
				label: "Search",
				popup: new dijit.TooltipDialog({content: searchForm.domNode}, dojo.place("<div></div>", dojo.body()))
			}));
			
			eventMenu.startup();
			
			_this.eventMenuNode = eventMenu.domNode;
		}
		else {
			dojo.query(_this.eventMenuNode.parentNode).orphan();
		}
	},
	setupUserMenu: function() {
		var _this = this;
		
		var m = new dijit.MenuBar({}, _this.userMenuNode);
		
		m.addChild(new dijit.MenuBarItem({
			label:"Logout",
			onClick: function() {
				window.location.href = hhreg.util.contextUrl("/admin/Login?a=logout");
			}
		}));
		m.startup();	
		
		_this.userMenuNode = m.domNode;
	}
});