<?php

class viewConverter_admin_report_GenerateReport extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= $this->getFileContents('page_admin_report_GenerateReport');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		$info = db_BreadcrumbManager::getInstance()->findGenerateReportCrumbs($this->info['reportId']);
		
		$crumbs = new fragment_Breadcrumb(array(
			'location' => 'GenerateReport', //ReportResults
			'eventCode' => $info['code'],
			'eventId' => $this->info['eventId'],
			'reportName' => $this->info['reportName']
		));
		
		return $crumbs->html();
	}
	
	public function getCsv($properties) {
		$this->setProperties($properties);
		
		$text = '"'.implode('","', $this->info['headings']).'"';
		$text .= PHP_EOL;
		
		foreach($this->info['rows'] as $row) {
			$text .= '"'.implode('","', $row['data']).'"';
			$text .= PHP_EOL;
		}
		
		$fileName = preg_replace('/\s+/', '_', $this->info['reportName']).'.csv';
		header('Content-Type: text/csv');
		header("Content-Disposition: attachment; filename=\"{$fileName}\"");
		
		return new template_TemplateWrapper($text);		
	}
}

?>