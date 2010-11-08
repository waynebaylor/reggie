
(function() {
	var _list = dojo.provide("hhreg.list");
	
	_list.bind = function(node) { 
		// up-down arrows
		dojo.query(".fragment-list .order-arrows a", node).connect("onclick", function(event) {
			// stop default behavior.
			dojo.stopEvent(event);
			
			dojo.xhrGet({
				url: event.currentTarget.href,
				handle: function(response) {
					_list.update(response, node);
				}
			});
		});
		
		// connect the remove links.
		dojo.query(".fragment-list a.remove", node).connect("onclick", function(event) {
			// stop default behavior.
			dojo.stopEvent(event);

			if(confirm("Are you sure?")) {
				dojo.xhrGet({
					url: event.currentTarget.href,
					handle: function(response) {
						_list.update(response, node);
					}
				});
			}
		});
	};

	_list.update = function(response, node) {
		// replace with new list.
		var fragmentList = dojo.query(".fragment-list", node)[0];
		var parent = fragmentList.parentNode.innerHTML = response;
		
		// connect event handlers to new list.
		_list.bind(node);
	};
})();