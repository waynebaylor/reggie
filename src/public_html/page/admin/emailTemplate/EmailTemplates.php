
<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");

	///////////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-email-templates").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
</script>
			
<div id="content">
	<div class="fragment-email-templates">
		<div>
			<?php echo $this->getFileContents('page_admin_emailTemplate_List') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Email Template', 
				'/admin/emailTemplate/EmailTemplates', 
				'addEmailTemplate', 
				$this->getFileContents('page_admin_emailTemplate_Add')
			) ?>
		</div>
	</div>
</div>

