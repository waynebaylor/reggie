
<script type="text/javascript">
	dojo.require("hhreg.util");
	dojo.require("dijit.form.Button");
	
	dojo.addOnLoad(function() {
		var eventId = dojo.byId("eventId").value;
		
		dojo.query("input[type=button]").forEach(function(item) {
			new dijit.form.Button({
				label: "Continue",
				onClick: function() {
					document.location = hhreg.util.contextUrl("/admin/dashboard/ConfirmDeleteEvent?a=deleteEvent&id="+eventId);
				}
			}, item);
		});
	});
</script>

<div id="content">
	<div class="confirm-delete-event">
		<div class="warning-text">
			<h3>Delete Event</h3>
			
			<p>
				Deleting this event will permanently remove all related data
				and registrations. 
			</p>	

			<p style="font-weight:bold;">ARE YOU SURE?</p>

			<div class="sub-divider"></div>
				
			<?php echo $this->HTML->hidden(array(
				'id' => 'eventId',
				'name' => 'id',
				'value' => $this->eventId
			)) ?>		
			
			<input type="button" title="Yes, delete the event" value="Continue">
			
			<?php echo $this->HTML->link(array(
				'label' => 'Cancel',
				'href' => '/admin/dashboard/MainMenu'
			)) ?>
			
			<div class="sub-divider"></div>
		</div>
	</div>
</div>
