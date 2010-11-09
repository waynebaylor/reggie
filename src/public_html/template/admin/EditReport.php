<?php

class template_admin_EditReport extends template_AdminPage
{
	private $event;
	private $report;
	
	function __construct($event, $report) {
		parent::__construct('Edit Report');
		
		$this->event = $event;
		$this->report = $report;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'Report',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code']
		));
	}
	protected function getContent() {
		$edit = new fragment_report_Edit($this->report);
		$fields = new fragment_report_field_Fields($this->event, $this->report);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.editReport");
			</script>
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
				
				{$fields->html()}
			</div>
_;
	}
}

?>