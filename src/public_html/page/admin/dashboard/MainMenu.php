
<script type="text/javascript">
	dojo.require("hhreg.calendar");
	dojo.require("hhreg.xhrAddList");
	
	(function() {
		dojo.provide("hhreg.admin.mainMenu");
		
		//////////////////////////////////////////////////
		
		dojo.addOnLoad(function() {
			dojo.query(".fragment-events").forEach(function(item) {
				hhreg.xhrAddList.bind(item);
			});
			
			dojo.query(".fragment-users").forEach(function(item) {
				hhreg.xhrAddList.bind(item);
			});
		});
	})();
</script>

<div id="content">
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



