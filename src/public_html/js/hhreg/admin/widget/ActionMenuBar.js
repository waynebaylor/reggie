dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuBar");
dojo.require("dijit.MenuItem");
dojo.require("dijit.PopupMenuBarItem");
dojo.require("dijit.MenuBarItem");

dojo.provide("hhreg.admin.widget.ActionMenuBar");

dojo.declare("hhreg.admin.widget.ActionMenuBar", [dijit._Widget, dijit._Templated], {
	showUsers: false,
	showEvents: false,
	showEventMenu: false,
	eventLabel: "",
	showReports: false,
	showRegForm: false,
	showBadgeTemplates: false,
	showFiles: false,
	showPages: false,
	baseClass: "reggie-admin-ActionMenuBar",
	templateString: dojo.cache("hhreg.admin.widget", "templates/ActionMenuBar.html"),
	postCreate: function() {
		this.inherited(arguments);
		
		this.setupGeneralMenu();
		this.setupEventLabel();
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
			eventsMenu.addChild(new dijit.MenuItem({
				label: "Create Event",
				onClick: function() {
					window.location.href = hhreg.util.contextUrl("/admin/event/CreateEvent");
				}
			}));
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
	},
	setupEventLabel: function() {
		var _this = this;
		
		var labelMenu = new dijit.MenuBar({}, _this.eventLabelNode);
				
		if(_this.showEventMenu) { 
			labelMenu.addChild(new dijit.MenuBarItem({
				label: _this.eventLabel,
				onClick: function() {}
			}));
		}
		
		labelMenu.startup();
		
		_this.eventLabelNode = labelMenu.domNode;
	},
	setupEventMenu: function() {
		var _this = this;
		
		var eventMenu = new dijit.MenuBar({}, _this.eventMenuNode);
		
		if(_this.showEventMenu) {
			if(_this.showReports) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Reports",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl(""); 
					}
				}));
			}
			if(_this.showRegForm) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Registration Form",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl(""); 
					}
				}));
			}
			if(_this.showBadgeTemplates) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Badge Templates",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl(""); 
					}
				}));
			}
			if(_this.showFiles) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Files",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl(""); 
					}
				}));
			}
			if(_this.showPages) {
				eventMenu.addChild(new dijit.MenuBarItem({
					label: "Pages",
					onClick: function() {
						window.location.href = hhreg.util.contextUrl(""); 
					}
				}));
			}
		}
		
		eventMenu.startup();
		
		_this.eventMenuNode = eventMenu.domNode;
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