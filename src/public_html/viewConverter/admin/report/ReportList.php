<?php

class viewConverter_admin_report_ReportList extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getView($properties) {
		$this->setProperties($properties);
		
		$html = <<<_
			<div id="report-grid"></div>
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.ReportGrid");
				
				new hhreg.admin.widget.ReportGrid({
					eventId: {$this->eventId}
				}, dojo.place("<div></div>", dojo.byId("report-grid"), "replace")).startup();
			</script>
_;

		return new template_TemplateWrapper($html);
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