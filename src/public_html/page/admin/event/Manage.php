
<script type="text/javascript">
	dojo.require("hhreg.admin.widget.EventTabs");
	dojo.require("hhreg.util");
	
	dojo.addOnLoad(function() {
		var placeholderNode = dojo.place('<div></div>', dojo.byId("event-tabs"), "replace");
		var eventId = dojo.query("input[name=eventId]")[0].value;

		dojo.xhrGet({
			url: hhreg.util.contextUrl("/admin/data/User?a=currentUser"),
			handleAs: "json",
			handle: function(response) {
				var tabs = new hhreg.admin.widget.EventTabs({
					user: response,
					eventId: eventId
				}, placeholderNode);

				tabs.startup();
			}
		});
	});
</script>

<style type="text/css">
	
</style>

<div id="content">
	<div class="fragment-edit">
		<h3>
			<?php echo $this->title ?>
		</h3>
		
		<div>
			Registration Sites: <a href="">Attendee</a> <a href="">Exhibitor</a> <a href="">Special</a>
		</div>
		
		<div class="sub-divider"></div>
		
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->event['id']
		)) ?>
		
		<div id="event-tabs"></div>
	</div>
</div>
