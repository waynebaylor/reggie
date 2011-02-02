
//
// reggie.profile.js should be located in util/buildscripts/profiles dir.
//
// prompt$ ./build.sh profile=reggie
//
// NOTE: the layer names result in the js files being created next to dojo.js. this is done to avoid a localization
//       error that happens when they're placed outside the dojo.js dir.

dependencies = {
	action: "release",
	version: "1.5.0",
	releaseName: "js",
	releaseDir: "../../../build-output",
	layers: [{
			name: "reggie_admin.js",
			dependencies: [
				"dijit.Calendar",
				"dijit._Widget",
				"dijit.Dialog",
				"hhreg.validation",
				"hhreg.list",
				"hhreg.xhrTableForm",
				"hhreg.xhrAddList",
				"hhreg.dialog",
				"hhreg.calendar",
				"hhreg.arrows",
				"hhreg.xhrLink",
				"hhreg.util",
				"hhreg.xhrEditForm"
			]
		},
		{
			name: "reggie_reg.js",
			dependencies: [
				"hhreg.util",
				"hhreg.validation"
			]
	}],
	prefixes: [
		["dijit", "../dijit"],
		["hhreg", "../hhreg"]
	]
}

