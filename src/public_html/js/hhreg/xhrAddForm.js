dojo.require("hhreg.validation");

(function() {
	var xhrAddForm = dojo.provide("hhreg.xhrAddForm");
	
	var setFocus = function(form) {
		var fields = dojo.query("input[type=text]", form);
		if(fields.length > 0) {
			fields[0].focus();
		}
		else {
			fields = dojo.query("textarea", form);
			if(fields.length > 0) {
				fields[0].focus();
			}
		}
	};
	
	var resetForm = function(/*DOM Node[form]*/ form) {
		dojo.query("input[type=text]", form).forEach(function(item) {
			item.value = "";
		});
		
		dojo.query("textarea", form).forEach(function(item) {
			item.value = "";
		});
		
		// first option is selected.
		dojo.query("select", form).forEach(function(item) {
			// unselect all options.
			dojo.query("option", item).forEach(function(opt) {
				opt.selected = false;
			});
			
			// select first option.
			item.options[0].selected = true;
		});
		
		// uncheck all checkboxes.
		dojo.query("input[type=checkbox]", form).forEach(function(item) {
			item.checked = false;
		});
		
		// uncheck all radio buttons.
		dojo.query("input[type=radio]", form).forEach(function(item) {
			item.checked = false;
		});
	};
	
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
	
	var submitForm = function(/*DOM Node[form]*/ form, /*DOM Node[.add-form]*/ formDiv, 
			                  /*DOM Node[.add-link]*/ addLink, /*function(optional)*/ callback) {
		hhreg.validation.removeMessages(form);
		
		var post = dojo.xhrPost({
			url: dojo.attr(form, "action"),
			content: dojo.formToObject(form),
			handleAs: "text"
		});
		
		post.addCallback(function(response) {
			var success = handleResponse(form, response);
			
			if(success) {
				dojo.addClass(formDiv, "hide");
				dojo.removeClass(addLink, "hide");
				resetForm(form);
				
				if(callback) {
					callback(response);
				}
			}
		});
		
		post.addErrback(function(error) {
			showErrorIcon(form);
		});
	};
	
	var handleResponse = function(/*DOM Node[form]*/ form, /*String*/ response) {
		var status = false;
		
		var div = dojo.create("div");
		dojo.addClass(div, "hide");
		div.innerHTML = response;
		dojo.body().appendChild(div);
		
		// if there was an error or a problem validating, then 
		// the response will be packed in a hidden input with 
		// the id 'xhr-response'. if there was no problem, then 
		// a normal response is sent and there will not be a 
		// node with id 'xhr-response'.
		var xhrResponse = dojo.byId("xhr-response");
		
		if(xhrResponse && xhrResponse.name === "error") {
			showErrorIcon(form);
			status = false;
		}
		else if(xhrResponse && xhrResponse.name === "validationError") {
			showValidationErrorIcon(form);
			hhreg.validation.showMessages(dojo.fromJson(xhrResponse.value), form);
			status = false;
		}
		else {
			hideIcons(form);
			status = true;
		}
		
		div.parentNode.removeChild(div);
		
		return status;
	};
	
	xhrAddForm.bind = function(/*DOM Node[.xhr-add-form]*/ node, /*function(optional)*/ callback) {
		var addLink = dojo.query(".add-link", node)[0];
		var formDiv = dojo.query(".add-form", node)[0];
		var form = dojo.query("form", formDiv)[0];
		var continueButton = dojo.query("input[type=button]", form)[0];
		var cancelLink = dojo.query(".cancel-link", form)[0];
		
		// show the form when user clicks add link.
		dojo.connect(addLink, "onclick", function() {
			dojo.addClass(addLink, "hide");
			dojo.removeClass(formDiv, "hide");
			setFocus(form);
			
		});
		
		// xhr form when user hits enter key. as if the continue button were
		// a submit button.
		dojo.connect(form, "onkeypress", function(event) {
			if(event.keyCode === dojo.keys.ENTER && event.target.tagName.toLowerCase() !== 'textarea') {
				dojo.stopEvent(event);
				submitForm(form, formDiv, addLink, callback);
			}
		});
		
		// xhr form when user clicks continue button.
		dojo.connect(continueButton, "onclick", function() {
			submitForm(form, formDiv, addLink, callback);
		});

		// hide the form when user clicks cancel link.
		dojo.connect(cancelLink, "onclick", function() {
			hhreg.validation.removeMessages(form);
			
			hideIcons(form);
			
			dojo.addClass(formDiv, "hide");
			dojo.removeClass(addLink, "hide");
			
			resetForm(form);
		});
	};
	
})();