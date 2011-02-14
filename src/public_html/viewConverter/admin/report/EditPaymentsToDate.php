<?php

class viewConverter_admin_report_EditPaymentsToDate extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Payments To Date Fields';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= $this->getFileContents('page_admin_report_EditPaymentsToDate');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		
	}
}

?>