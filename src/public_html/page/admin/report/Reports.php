
<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-reports").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});
	});
</script>

<div id="content">
	<div class="fragment-reports">
		<div>
			<?php echo $this->getFileContents('page_admin_report_ReportList') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Report', 
				'/admin/report/Reports',
				'addReport',
				$this->getFileContents('page_admin_report_AddReport')
			) ?>
		</div>
	</div>
</div>





