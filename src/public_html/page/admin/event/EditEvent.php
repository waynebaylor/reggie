
<script type="text/javascript">
	dojo.require("hhreg.xhrEditForm");
	dojo.require("hhreg.calendar");
	dojo.require("hhreg.xhrAddList");

	dojo.addOnLoad(function() {
		dojo.query(".fragment-pages").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
</script>

<div id="content">
	<div class="fragment-edit">
		<h3>
			<?php echo $this->title ?>
		</h3>
		
		<?php echo $this->HTML->link(array(
			'label' => 'Appearance',
			'href' => '/admin/event/EditAppearance',
			'title' => 'Edit event appearance',
			'parameters' => array(
				'action' => 'view',
				'eventId' => $this->event['id']
			)
		)) ?>
		&nbsp;
		<?php echo $this->HTML->link(array(
			'label' => 'Payment Options',
			'href' => '/admin/event/EditPaymentOptions',
			'title' => 'Edit event payment options',
			'parameters' => array(
				'action' => 'view',
				'id' => $this->event['id']
			)
		)) ?>
		&nbsp;
		<?php echo $this->HTML->link(array(
			'label' => 'Email Templates',
			'href' => '/admin/emailTemplate/EmailTemplates',
			'title' => 'Event Email Templates',
			'parameters' => array(
				'a' => 'view',
				'eventId' => $this->event['id']
			)
		)) ?>
		&nbsp;
		<?php echo $this->HTML->link(array(
			'label' => 'Group Registration',
			'href' => '/admin/event/EditGroupRegistration',
			'title' => 'Edit event group registration options.',
			'parameters' => array(
				'a' => 'view',
				'id' => $this->event['id']
			)
		)) ?>
		
		<div class="sub-divider"></div>
		
		<?php echo $this->xhrTableForm(
			'/admin/event/EditEvent', 
			'saveEvent', 
			$this->getFileContents('page_admin_event_Edit')
		) ?>
	</div>
	
	<div class="divider"></div>
	
	<div class="fragment-pages">
		<div>
			<?php echo $this->getFileContents('page_admin_event_PageList') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Page', 
				'/admin/page/Page', 
				'addPage', 
				$this->getFileContents('page_admin_event_AddPage')
			) ?>
		</div>
	</div>
</div>

