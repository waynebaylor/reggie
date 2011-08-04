dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuBar");
dojo.require("dijit.MenuItem");
dojo.require("dijit.PopupMenuBarItem");

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
		
		var userMenu;
		if(_this.showUsers) {
			userMenu = new dijit.Menu({});
			userMenu.addChild(new dijit.MenuItem({label: "Create"}));
			userMenu.addChild(new dijit.MenuItem({label: "Edit"}));
			
			menu.addChild(new dijit.PopupMenuBarItem({label: "Users", popup: userMenu}));
		}

		menu.startup();
		
		_this.menuNode = menu.domNode;
	}
});