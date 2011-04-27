
<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");

	dojo.addOnLoad(function() {
		dojo.query(".fragment-pages").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
</script>

<div id="content">
	<div class="fragment-pages">
		<div>
			<?php echo $this->getFileContents('page_admin_staticPage_List') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Page',
				'/admin/staticPage/PageList',
				'addPage',
				$this->getFileContents('page_admin_staticPage_Add')
			) ?>
		</div>
	</div>
</div>
