
<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");

	///////////////////////////////////
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-report-fields").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
</script>

<div id="content">
	<div class="fragment-report-fields">
		<div>
			<?php echo $this->getFileContents('page_admin_report_paymentsToDate_List') ?>
		</div>	
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Report Field',
				'/admin/report/EditPaymentsToDate',
				'addField',
				$this->getFileContents('page_admin_report_paymentsToDate_Add')
			) ?>
		</div>
	</div>
</div>



