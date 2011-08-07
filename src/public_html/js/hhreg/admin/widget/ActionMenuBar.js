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
	baseClass: "reggie-admin-ActionMenuBar",
	templateString: '<div><div data-dojo-attach-point="menuNode"></div></div>',
	postCreate: function() {
		this.inherited(arguments);
		
		var _this = this;

		var menu = new dijit.MenuBar({}, _this.menuNode);
		
		menu.addChild(new dijit.MenuBarItem({label: ""})); // spacer for when there are no menu items.
		
		var usersMenu;
		if(_this.showUsers) {
			usersMenu = new dijit.Menu({});
			usersMenu.addChild(new dijit.MenuItem({label: "Create User"}));
			usersMenu.addChild(new dijit.MenuItem({
				label: "Edit Users",
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
			eventsMenu.addChild(new dijit.MenuItem({label: "Create Event"}));
			eventsMenu.addChild(new dijit.MenuItem({label: "Edit Events"}));
			
			menu.addChild(new dijit.PopupMenuBarItem({
				label: '<span class="general-item">Events</span>', 
				popup: eventsMenu
			}));
		}

		menu.startup();
		
		_this.menuNode = menu.domNode;
	}
});