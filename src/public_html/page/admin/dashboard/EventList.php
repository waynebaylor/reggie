
<script type="text/javascript">
	dojo.require("hhreg.dialog");
	dojo.require("hhreg.xhrTableForm");
	dojo.require("hhreg.util");

	dojo.addOnLoad(function() {
		var dialog = hhreg.dialog.create({
			title: 'Create Registration',
			content: dojo.byId("create-reg-content"),
			onClose: function() {
				hhreg.xhrTableForm.hideIcons(createRegForm)
			}
		});

		var createRegForm = dojo.query("form", dialog.domNode)[0];
		hhreg.xhrTableForm.bind(createRegForm);
		
		// create registration links and form dialog.
		dojo.query(".create-reg-link").forEach(function(createRegLink) {
			dojo.connect(createRegLink, "onclick", function() {
				// remove any existing eventId values.
				dojo.query("input[name=eventId]", createRegForm).orphan();
	
				// add the desired eventId to the dialog form.
				var eventId = dojo.query("input[name=eventId]", createRegLink.parentNode)[0].value;
				var eventIdHtml = dojo.string.substitute('<input type="hidden" name="eventId" value="${eventId}">', {eventId: eventId});
				dojo.place(eventIdHtml, createRegForm);
	
				dialog.show();
			});
		});
	});
</script>

<h3>Events</h3>

<div id="create-reg-content" class="hide" style="padding:0px;">
	<?php echo $this->xhrTableForm(
		'/admin/dashboard/MainMenu',
		'createRegistration',
		$this->getFileContents('page_admin_dashboard_CreateRegistrationForm'),
		'Continue',
		'There was a problem saving. Please try again.',
		false
	) ?>
</div>

<div class="fragment-list">
	<table class="admin">
		<?php if(empty($this->events)): ?>
		<tr><td>No Events</td></tr>
		<?php else: ?>
		<?php foreach($this->events as $eventInfo): ?>
		<tr>
			<td>
				<?php echo "{$eventInfo['event']['displayName']} ({$eventInfo['event']['code']})" ?>
			</td>
			<td>
				<?php echo ucfirst($eventInfo['status']) ?>
			</td>
			<td>
				<?php foreach(model_Category::values() as $category): ?>
				<?php $pages = model_EventPage::getVisiblePages($eventInfo['event'], $category);
					  if(!empty($pages)): ?>
				<?php echo $this->HTML->link(array(
					'label' => $category['displayName'],
					'href' => "/event/{$eventInfo['event']['code']}/".model_Category::code($category),
					'title' => 'As seen by '.$category['displayName'],
					'target' => '_blank'
				)) ?>
				<?php endif; ?>
				<?php endforeach; ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Edit',
					'href' => '/admin/event/EditEvent',
					'parameters' => array(
						'a' => 'view',
						'id' => $eventInfo['event']['id']
					),
					'title' => 'Edit Event'
				)) ?>
				
				<?php echo $this->HTML->link(array(
					'label' => 'Reports',
					'href' => '/admin/report/Reports',
					'parameters' => array(
						'a' => 'view',
						'id' => $eventInfo['event']['id']
					),
					'title' => 'Event Reports'
				)) ?>
				
				<span>
					<span class="create-reg-link link">Create Registration</span>
					<?php echo $this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $eventInfo['event']['id']
					)) ?>
				</span>
				
				<?php echo $this->HTML->link(array(
					'label' => 'Files',
					'href' => '/admin/fileUpload/FileUpload',
					'parameters' => array(
						'a' => 'view',
						'id' => $eventInfo['event']['id']
					),
					'title' => 'Event Files'
				)) ?>
				
				<?php echo $this->HTML->link(array(
					'label' => 'Badge Templates',
					'href' => '/admin/badge/BadgeTemplates',
					'parameters' => array(
						'a' => 'view',
						'eventId' => $eventInfo['event']['id']
					),
					'title' => 'Badge Templates/Printing'
				)) ?>
				
				<?php echo $this->HTML->link(array(
					'label' => 'Delete',
					'href' => '/admin/dashboard/ConfirmDeleteEvent',
					'parameters' => array(
						'a' => 'view',
						'id' => $eventInfo['event']['id']
					),
					'style' => 'margin-left:15px;'
				)) ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</table>	
</div>

