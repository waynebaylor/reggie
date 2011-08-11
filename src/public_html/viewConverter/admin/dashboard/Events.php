<?php

class viewConverter_admin_dashboard_Events extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Manage Events';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_dashboard_events_Main');
		
		return $body;
	}
	
	public function getListEvents($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_dashboard_events_List'));
	}
}

?>