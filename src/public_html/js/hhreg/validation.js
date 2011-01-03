dojo.require("hhreg.util");

(function() {
	var _validation = dojo.provide("hhreg.validation");
	
	var getInputLabel = function(/*DOM Node*/ node) {
		if(node.id) {
			var labels = dojo.query("label").filter(function(item) {
				return dojo.attr(item, "for") === node.id;
			});
			
			return labels[0];
		}
		
		return null;
	};
	
	var placeGeneralError = function(/*DOM Node[div.error-message]*/ div) {
		dojo.style(div, "position", "static");
		node.appendChild(div);
	};
	
	var placeCheckboxOrRadioError = function(/*DOM Node[div.error-message]*/ div, /*DOM Node*/ node) {
		// put the message above the input options.
		//
		// this assumes the checkbox/radio input is in a table with CSS class
		// "checkbox-label" or "radio-label".
		
		var parent = hhreg.util.parentNode(node, ["checkbox-label", "radio-label"]);
		
		dojo.style(div, {
			position: "static",
			padding: "0px"
		});
		dojo.place(div, parent, "before");
	};
	
	var placeCalendarError = function(/*DOM Node[div.error-message]*/ div, /*DOM Node*/ node) {
		// display calendar messages to the right of the calendar img.
		
		placeError(div, hhreg.util.parentNode(node, ["hhreg-calendar"]));
	};
	
	var placeError = function(/*DOM Node[div.error-message]*/ div, /*DOM Node*/ node) {
		// if the form input has a label associated with it, then 
		// show the message next to the label (this assumes that 
		// the label to the right of the input). 
		
		node = getInputLabel(node) || node;
		
		position = dojo.position(node, true);
		dojo.style(div, {
			top: position.y+"px",
			left: (position.x+position.w)+"px"
		});
		
		// if the node is a form element, then
		// place the message in the form. this
		// makes it possible to remove all the 
		// messages associated with a form.
		if(node.form) {
			node.form.appendChild(div);
		}
		// otherwise put it in the body.
		else {
			dojo.body().appendChild(div);
		}
	};
	
	var createErrorMessage = function(/*DOM Node[div|input|select|textarea]*/ node, /*String|array*/ text) {
		//
		// summary:
		//         create a validation message for the given form
		//         input. the text will be displayed next to the 
		//         node with a validation error icon.
		// node:
		//         a div (general errors) or form element node. the validation message will
		//         be displayed next to this node.
		// text:
		//         the validation message text.
		//
		
		var div = dojo.create("div");
		dojo.addClass(div, "error-message");
		
		var img = dojo.create(
			"img", {
				src: hhreg.contextUrl("/images/caution_red.gif"), 
				alt: "Validation Error", 
				title: "Validation Error"
		});
		div.appendChild(img);
		
		var span = dojo.create("span");
		dojo.addClass(span, "error-text");
		span.appendChild(document.createTextNode(" "+text));
		div.appendChild(span);
	
		var position;
		
		if(node.id === "general-errors") {
			placeGeneralError(div);
		}
		else if(hhreg.util.parentNode(node, ["hhreg-calendar"])) {
			placeCalendarError(div, node);
		}
		else if(hhreg.util.parentNode(node, ["checkbox-label", "radio-label"])) {
			placeCheckboxOrRadioError(div, node);
		}
		else {
			placeError(div, node);
		}
	};
	
	_validation.removeMessages = function(/*DOM Node[form]*/ form) {
		// 
		// summary:
		//         removes all validation messages.
		//
		// form: (optional) a form node. if given, then only error
		//       messages in the form will be removed.
		//
		
		if(form) {
			dojo.query(".error-message", form).orphan();
		}
		else {
			dojo.query(".error-message").orphan();
		}
	};
	
	_validation.showMessages = function(/*JSON*/ messages, /*DOM Node[form]*/ form) {
		//
		// summary:
		//         displays validation icon and message next 
		//         to invalid inputs.
		// messages:
		//         a JSON object associating input field names
		//         to validation message. for example:
		//             {firstName: "First Name is required."}
		// form:   (optional) the form in which to display the messages. if
		//         not given, then the message will be applied to the first 
		//         node with the given name.
		//         
		
		var node;
		var field;
		for(fieldName in messages) {	
			if(fieldName === 'general') {
				node = dojo.byId("general-errors");
			}
			else if(form) {
				for(var i=0; i<form.elements.length; ++i) {	
					// the form input name may have a '[]' appended to the end.
					if(form.elements[i].name === fieldName) {
						node = form.elements[i];
						break; // put the message on the first input with that name
					}
				}
			}
			else {
				node = document.getElementsByName(fieldName)[0];
			}
			
			createErrorMessage(node, messages[fieldName]);
		}
	};
})();