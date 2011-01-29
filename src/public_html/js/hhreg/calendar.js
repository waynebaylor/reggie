dojo.require("dijit.Calendar");
dojo.require("dijit._Widget");

(function() {
	dojo.provide("hhreg.calendar");
	
	var allCalendars = [];
	
	var closeAllCalendars = function() {
		dojo.forEach(allCalendars, function(c) {
			dijit.popup.close(c)
		});
	};
	
	dojo.declare("hhreg.Calendar", [dijit._Widget], {
		postCreate: function() {
			var input = dojo.query("input", this.domNode)[0];
			var img = dojo.query("img", this.domNode)[0];
			
			var cal = new dijit.Calendar({
				onValueSelected: function(date) {
					input.value = dojo.date.locale.format(date, {
						selector: "date",
						datePattern: "yyyy-MM-dd"
					});
				}
			});
			
			dijit.popup.moveOffScreen(cal.domNode);
			
			allCalendars.push(cal);
			
			var hhregCal = this;
			dojo.connect(img, "onclick", function() {
				closeAllCalendars();
				
				dijit.popup.open({
					popup: cal,
					parent: hhregCal,
					around: img,
					orient: {BR: "TL", TR: "BL", BL: "TR", TL: "BR"},
					onClose: function() { input.focus(); },
					onExecute: function() { dijit.popup.close(cal); }
				});
			});
			
			this.calendarWidget = cal;
		},
		_onBlur: function() {
			dijit.popup.close(this.calendarWidget);
		}
	});
	
	//////////////////////////////////////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".hhreg-calendar").forEach(function(item) {
			var c = new hhreg.Calendar({}, item);
			c.startup();
		});
	});
})();				
				