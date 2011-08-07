
<script type="text/javascript">
	dojo.require("hhreg.util");
	dojo.require("hhreg.calendar");
	dojo.require("hhreg.xhrAddList");
	dojo.require("dijit.form.Button");
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-events").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
		
		dojo.query(".fragment-users").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});

		// setup big buttons.
		dojo.query("#users-button").forEach(function(item) {
			new dijit.form.Button({
				label: '<div class="button-wrapper"><div class="button-title">Users</div><div class="button-subtext">Create, Delete, and Manage Users</div></div>',
				onClick: function() {
					window.location.href = hhreg.util.contextUrl("/admin/dashboard/Users");
				}
			}, item).startup();
		});
		dojo.query("#events-button").forEach(function(item) {
			new dijit.form.Button({
				label: '<div class="button-wrapper"><div class="button-title">Events</div><div class="button-subtext">Create, Delete, and Manage Events</div></div>',
				onClick: function() {}
			}, item).startup();
		});
	});
</script>

<style type="text/css">
	.button-wrapper {
		width: 300px;
		padding: 15px 20px;
	}
	
	.button-title {
		text-align: left;
		font-size: 24px;
	}
	
	.button-subtext {
		text-align: left;
		font-size: 10px;
		font-style: italic;
		white-space: nowrap;
	}
</style>

<div id="content">

	<?php if($this->showUsersMenu): ?>
		<div id="users-button"></div>
	
		<div class="sub-divider"></div>
	<?php endif; ?>
	
	<?php if($this->showEventsMenu): ?>
		<div id="events-button"></div>
	<?php endif; ?>


<div class="divider"></div>
<div class="divider"></div>
<div class="divider"></div>
<div class="divider"></div>





	<div class="fragment-events">
		<div>
			<?php echo $this->getFileContents('page_admin_dashboard_EventList') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Event',
				'/admin/dashboard/MainMenu',
				'addEvent',
				$this->getFileContents('page_admin_dashboard_AddEvent')
			) ?>
		</div>
	</div>
		
	<div class="divider"></div>	
		
	<?php if($this->userIsAdmin): ?>
	<div class="fragment-users">
		<div>
			<?php echo $this->getFileContents('page_admin_dashboard_UserList') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add User',
				'/admin/dashboard/MainMenu',
				'addUser',
				$this->getFileContents('page_admin_dashboard_AddUser')
			) ?>
		</div>
	</div>
	<?php endif; ?>
</div>



