
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
				"dojo.data.ItemFileReadStore",
				"dojo.date.locale",
				"dojo.cache",
				"dojo.string",
			    "dijit.Editor",
				"dijit.Calendar",
				"dijit._Widget",
				"dijit.Dialog",
				"dijit.layout.TabContainer",
				"dojox.form.BusyButton",
				"dojox.grid.EnhancedGrid",
				"dojox.grid.enhanced.plugins.Pagination",
				"dojox.grid.enhanced.plugins.IndirectSelection",
				"dojox.layout.ContentPane",
				"hhreg.admin.widget.ActionMenuBar",
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

