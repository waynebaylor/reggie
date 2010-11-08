<?php

class template_admin_Reports extends template_AdminPage
{
	private $event;
	
	function __construct($event) {
		parent::__construct('Reports');
		
		$this->event = $event;
	}
	
	protected function getContent() {
		$breadcrumbs = new fragment_Breadcrumb(array(
			'location' => 'Reports',
			'event' => $this->event
		));
		
		$reports = new fragment_report_Reports($this->event);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.reports");
			</script>
			
			<div id="content">
				{$reports->html()}
				
				<div class="divider"></div>
				
				{$breadcrumbs->html()}
			</div>
_;
	}
}

?>