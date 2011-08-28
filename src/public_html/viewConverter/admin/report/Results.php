<?php

class viewConverter_admin_report_Results extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getView($properties) {
		$this->setProperties($properties);
		
		$json = $this->getFileContents('page_admin_data_ReportResults');
		return new template_TemplateWrapper($json);
	}
}

?>