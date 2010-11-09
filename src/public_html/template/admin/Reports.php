<?php

class template_admin_Reports extends template_AdminPage
{
	private $event;
	
	function __construct($event) {
		parent::__construct('Reports');
		
		$this->event = $event;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'Reports',
			'eventCode' => $this->event['code']
		));
	}
	
	protected function getContent() {
		$reports = new fragment_report_Reports($this->event);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.reports");
			</script>
			
			<div id="content">
				{$reports->html()}
			</div>
_;
	}
}

?>