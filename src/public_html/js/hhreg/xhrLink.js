
(function() {
	var xhrLink = dojo.provide("hhreg.xhrLink");

	xhrLink.bind = function(/*DOM Node[.xhr-link]*/ node, /*function*/ callback) {
		var link = dojo.query(".link", node)[0];
		var form = dojo.query("form", node)[0];

		dojo.connect(link, "onclick", function() {
			dojo.xhrPost({
				url: dojo.attr(form, "action"),
				content: dojo.formToObject(form),
				handle: callback
			});
		});
	};

})();