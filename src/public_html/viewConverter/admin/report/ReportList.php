<?php

class viewConverter_admin_report_ReportList extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Reports';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.ReportGrid");
				
				dojo.addOnLoad(function() {
					new hhreg.admin.widget.ReportGrid({
						eventId: {$this->eventId},
						showCreateLink: {$this->showControls},
						showDeleteButton: {$this->showControls}
					}, dojo.place("<div></div>", dojo.byId("report-grid"), "replace")).startup();
				});
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>{$this->title}</h3>
					
					<div id="report-grid"></div>
				</div>
			</div>
_;

		return $body;
	}
	
	public function getListReports($properties) {
		$this->setProperties($properties);
		
		$html = $this->getFileContents('page_admin_data_Reports');
		return new template_TemplateWrapper($html);
	}
	
	public function getDeleteReports($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>