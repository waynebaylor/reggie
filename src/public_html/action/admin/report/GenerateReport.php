<?php

class action_admin_report_GenerateReport extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_GenerateReport();
		$this->converter = new viewConverter_admin_report_GenerateReport();
	}
	
	public function view() {
		$reportId = RequestUtil::getValue('id', 0);
		
		$reportInfo = $this->logic->view($reportId);
		
		return $this->converter->getView(array(
			'title' => $reportInfo['reportName'],
			'info' => $reportInfo
		));
	}
	
	public function csv() {
		$reportId = RequestUtil::getValue('id', 0);
		
		$reportInfo = $this->logic->csv($reportId);
		
		return $this->converter->getCsv(array(
			'info' => $reportInfo
		));
	}
	
	public function search() {
		$reportId = RequestUtil::getValue('reportId', 0);
		$eventId = RequestUtil::getValue('eventId', 0);
		$term = RequestUtil::getValue('term', '');
		$field = RequestUtil::getValue('field', '');
		
		error_log(print_r(array($reportId, $eventId, $term, $field), true));
		
		
		
		
		$reportInfo = $this->logic->view($reportId);
		
		return $this->converter->getView(array(
			'title' => $reportInfo['reportName'],
			'info' => $reportInfo
		));
	}
}

?>