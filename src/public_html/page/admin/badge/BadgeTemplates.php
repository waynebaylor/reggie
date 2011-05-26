
<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");

	dojo.addOnLoad(function() {
		dojo.query(".fragment-templates").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
</script>

<div id="content">
	<div class="fragment-templates">
		<div>
			<?php echo $this->getFileContents('page_admin_badge_TemplateList') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Badge Template',
				'/admin/badge/BadgeTemplates',
				'addTemplate',
				$this->getFileContents('page_admin_badge_AddTemplate')
			) ?>
		</div>
	</div>
</div>


