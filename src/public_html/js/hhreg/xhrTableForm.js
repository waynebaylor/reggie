dojo.require("hhreg.validation");

(function() {
	var xhrTableForm = dojo.provide("hhreg.xhrTableForm");
	
	var hideIcons = function(/*DOM Node*/ node) {
		dojo.query(".xhr-save-success", node)
			.addClass("hide")
			.removeClass("validation-icon");
		dojo.query(".xhr-save-error", node)
			.addClass("hide")
			.removeClass("validation-icon");
		dojo.query(".xhr-validation-error", node)
			.addClass("hide")
			.removeClass("validation-icon");
	};

	var showSuccessIcon = function(/*DOM Node*/ node) {
		hideIcons(node);
		dojo.query(".xhr-save-success", node)
			.addClass("validation-icon")
			.removeClass("hide");
	};
	
	var showErrorIcon = function(/*DOM Node*/ node) {
		hideIcons(node);
		dojo.query(".xhr-save-error", node)
			.addClass("validation-icon")
			.removeClass("hide");
	};
	
	var showValidationErrorIcon = function(/*DOM Node*/ node) {
		hideIcons(node);
		dojo.query(".xhr-validation-error", node)
			.addClass("validation-icon")
			.removeClass("hide");
	};
	
	var submitForm = function(/*DOM Node[form]*/ form, /*function(optional)*/ callback) {
		// remove any previous error messages.
		hhreg.validation.removeMessages(form);
	
		var post = dojo.xhrPost({
			url: dojo.attr(form, "action"),
			content: dojo.formToObject(form),
			handleAs: "text"
		});
		
		post.addCallback(function(response) { 
			var success = handleResponse(form, response);

			if(success && callback) {
				callback(response);
			}
		});
		
		post.addErrback(function(error) { 
			showErrorIcon(form);
		});
	};
	
	var handleResponse = function(/*DOM Node*/ form, /*String*/ response) {
		var status = false;
		
		var div = dojo.create("div", {innerHTML: response});
		dojo.addClass(div, "hide");
		dojo.body().appendChild(div);
		
		// if there was an error or a problem validating, then 
		// the response will be packed in a hidden textarea with 
		// the id 'xhr-response'. if there was no problem, then 
		// a normal response is sent and there will not be a 
		// node with id 'xhr-response'.
		var xhrResponse = dojo.byId("xhr-response");
		
		if(xhrResponse && xhrResponse.name === "validationError") {
			showValidationErrorIcon(form);
			hhreg.validation.showMessages(dojo.fromJson(xhrResponse.value), form);
			status = false;
		}
		else if(xhrResponse && xhrResponse.name === 'success') {
			showSuccessIcon(form);
			
			setTimeout(function() {
				hideIcons();
			}, 5000);
			
			status = true;
		}
		else {
			showErrorIcon(form);
			status = false;
		}
		
		div.parentNode.removeChild(div);
		
		return status;
	};
	
	xhrTableForm.bind = function(/*DOM Node[form]*/ form, /*function(optional)*/ callback) {
		// xhr form when user hits enter key. as if the continue button were
		// a submit button.
		dojo.connect(form, "onkeypress", function(event) {
			if(event.keyCode === dojo.keys.ENTER && event.target.tagName.toLowerCase() !== 'textarea') {
				dojo.stopEvent(event);
				submitForm(form, callback);
			}
		});
		
		dojo.query("input[type=button]", form).connect("onclick", function() {
			submitForm(form, callback);
		});
	};
	
})();