
<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");
	dojo.require("dijit.Dialog");
	dojo.require("dijit.form.Button");
	
	dojo.addOnLoad(function() {
		dojo.query(".fragment-reports").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});

		//////////////////////////////////////////////////////
		// search links and form dialog.
		var searchContent = dojo.byId("search-form-content");
		var searchForm = dojo.query("form", searchContent)[0];
		var d = new dijit.Dialog({
			title: "Search Report",
			content: searchContent,
			duration: 150
		});
		d.startup();
		dojo.removeClass(searchContent, "hide");

		var plainSearchButton = dojo.query("input[type=button]", searchContent)[0];
		var searchButton = new dijit.form.Button({
			label: plainSearchButton.value,
			onClick: function() {
				searchForm.submit();
			}
		}, plainSearchButton);
		searchButton.startup();

		dojo.connect(searchForm, "onkeypress", function(event) {
			if(event.keyCode === dojo.keys.ENTER && event.target.tagName.toLowerCase() !== 'textarea') {
				dojo.stopEvent(event);
				searchForm.submit();
			}
		});
		
		dojo.query(".search-form-link").forEach(function(searchLink) {
			// show dialog when user clicks link.
			dojo.connect(searchLink, "onclick", function() {
				// set the reportId value.
				var reportId = dojo.query("input", searchLink)[0].value;
				dojo.query("input[name=reportId]", searchForm)[0].value = reportId;
				
				d.show();
			});
		});
	});
</script>

<div id="content">
	<div id="search-form-content" class="hide">
		<?php echo $this->tableForm(         
			'/admin/report/GenerateReport',
			'search',
			$this->getFileContents('page_admin_report_SearchForm'),
			'Search'
		) ?>
	</div>
	
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





