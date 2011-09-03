
<script type="text/javascript">
	dojo.require("hhreg.util");
	dojo.require("dijit.form.Button");
	
	dojo.addOnLoad(function() {
		dojo.query("input[type=button]").forEach(function(item) {
			new dijit.form.Button({
				label: "Continue",
				title: item.title,
				onClick: function() {
					document.forms[0].submit();
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
				Deleting the event(s) will permanently remove all related data
				and registrations. 
			</p>	

			<p style="font-weight:bold;">ARE YOU SURE?</p>

			<div class="sub-divider"></div>
				
			<form method="post" action="<?php echo $this->contextUrl('/admin/dashboard/ConfirmDeleteEvent') ?>">
				<?php echo $this->HTML->hidden(array(
					'name' => 'a',
					'value' => 'deleteEvents'
				)) ?>
				
				<?php foreach($this->eventIds as $eventId): ?>
					<?php echo $this->HTML->hidden(array(
						'name' => 'eventIds[]',
						'value' => $eventId
					)) ?>		
				<?php endforeach; ?>
				
				<input type="button" title="Yes, delete the event(s)" value="Continue">
				
				<?php echo $this->HTML->link(array(
					'label' => 'Cancel',
					'href' => '/admin/Login'
				)) ?>
			</form>
			
			<div class="sub-divider"></div>
		</div>
	</div>
</div>
