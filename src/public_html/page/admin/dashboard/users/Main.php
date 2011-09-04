
<script type="text/javascript">
	dojo.require("hhreg.admin.widget.UsersGrid");
	
	dojo.addOnLoad(function() {
		var userGrid = new hhreg.admin.widget.UsersGrid({}, dojo.place("<div></div>", dojo.byId("user-list"), "replace"));
		userGrid.startup();
	});
</script>

<div id="content">

	<div class="fragment-edit">
		<h3>Manage Users</h3>
		<div id="user-list"></div>
	</div>
	
</div>

