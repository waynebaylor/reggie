
//
// reggie.profile.js should be located in util/buildscripts/profiles dir.
//
// prompt$ ./build.sh profile=reggie
//
// NOTE: the layer names result in the js files being created next to dojo.js. this is done to avoid a localization
//       error that happens when they're placed outside the dojo.js dir.

dependencies = {
	action: "release",
	version: "1.6.1",
	releaseName: "js",
	releaseDir: "../../../build-output",
	cssOptimize: "comments",
	layers: [
	    {
	    	name: "reggie_login.js",
	    	dependencies: [
	    	    "dijit.MenuBar",
	    	    "dijit.MenuBarItem",
	    	    "hhreg.xhrEditForm"
	    	]
	    },
	    {
			name: "reggie_admin.js",
			dependencies: [
			    "dijit.Editor",
				"dijit.Calendar",
				"dijit._Widget",
				"dijit.Dialog",
				"dojox.form.BusyButton",
				"dojo.data.ItemFileReadStore",
				"dojox.grid.EnhancedGrid",
				"dojox.grid.enhanced.plugins.Pagination",
				"hhreg.admin.widget.ActionMenuBar",
				"hhreg.admin.widget.EventsGrid",
				"hhreg.validation",
				"hhreg.list",
				"hhreg.xhrTableForm",
				"hhreg.xhrAddList",
				"hhreg.xhrAddForm",
				"hhreg.dialog",
				"hhreg.calendar",
				"hhreg.xhrLink",
				"hhreg.util"
			]
		},
		{
			name: "reggie_reg.js",
			dependencies: [
				"hhreg.util",
				"hhreg.validation"
			]
		}
	],
	prefixes: [
		["dijit", "../dijit"],
		["dojox", "../dojox"],
		["hhreg", "../hhreg"]
	]
}

