
<script type="text/javascript">
	dojo.require("hhreg.xhrEditForm");
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-fields").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
</script>

<div id="content">
	<div class="fragment-edit">
		<h3>
			<?php echo $this->title ?>
		</h3>
		
		<?php echo $this->xhrTableForm(
			'/admin/report/EditReport',
			'saveReport',
			$this->getFileContents('page_admin_report_Edit')
		) ?>
	</div>

	<div class="divider"></div>
	
	<div class="fragment-fields">
		<div>
			<?php echo $this->getFileContents('page_admin_report_FieldList') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Field',
				'/admin/report/EditReport',
				'addField',
				$this->getFileContents('page_admin_report_AddField')
			) ?>
		</div>
	</div>
</div>




