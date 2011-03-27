<?php

class viewConverter_admin_report_Reports extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	protected function body() {
		$body = parent::body();
	
		$body .= $this->getFileContents('page_admin_report_Reports');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		$b = new fragment_Breadcrumb(array(
			'location' => 'Reports',
			'eventCode' => $this->event['code']
		));
		
		return $b->html();
	}
	
	public function getAddReport($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_report_ReportList'));
	}
	
	public function getRemoveReport($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_report_ReportList'));
	}
}

?>