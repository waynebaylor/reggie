
//
// reggie.profile.js should be located in util/buildscripts/profiles dir.
//
// prompt$ ./build.sh profile=reggie
//

dependencies = {
	action: "release",
	version: "1.5.0",
	releaseName: "js",
	releaseDir: "../../build-output",
	layers: [{
			name: "../admin.js",
			resourceName: "admin",
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
			name: "../reg.js",
			resourceName: "reg",
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

