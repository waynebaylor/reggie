<?php

class viewConverter_admin_report_PaymentsToDate extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Payments To Date';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= $this->getFileContents('page_admin_report_PaymentsToDate');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		
	}
	
	public function getCsv($properties) {
		$this->setProperties($properties);
		
		$text = '';
		foreach($this->data as $line) {
			$text .= implode(',', $line);
			$text .= PHP_EOL;
		}
		
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="payments_to_date.csv"');
		
		return new template_TemplateWrapper($text);
	}
}

?>