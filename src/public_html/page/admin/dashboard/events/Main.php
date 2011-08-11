
<script type="text/javascript">
	dojo.require("hhreg.admin.widget.EventsGrid");

	dojo.addOnLoad(function() {
		var grid = new hhreg.admin.widget.EventsGrid({}, dojo.place("<div></div>", dojo.byId("event-list"), "replace"));
		grid.startup();
	});
</script>

<div id="content">

	<h3>Events</h3>
	<div id="event-list"></div>
	
</div>

