
dojo.require("dijit.InlineEditBox");
dojo.require("dijit.form.Textarea");

dojo.provide("hhreg.admin.staticPage");

dojo.addOnLoad(function() {
	var input = dojo.byId("static-content-input");
	var value = dojo.byId("static-content-value");

	input.value = value.innerHTML;
	
	var e = new dijit.InlineEditBox({
		editor: "dijit.form.Textarea",
		value: value.innerHTML,
		buttonSave: "Preview",
		autoSave: false,
		renderAsHtml: true,
		noValueIndicator: '<span style="color:#666;"><b>Click to enter content.</b> Any HTML you type will be rendered when you click &quot;Preview&quot;.</span>',
		onChange: function(text) {
			input.value = text;
		}
	}, dojo.byId("static-page-content"));	
	e.startup();

	value.parentNode.removeChild(value);
});