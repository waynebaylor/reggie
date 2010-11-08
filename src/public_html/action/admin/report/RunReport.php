<?php

class action_admin_report_RunReport extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$report = $this->strictFindById(db_ReportManager::getInstance(), RequestUtil::getValue('id', 0));
		
		$fieldHeadings = db_ReportManager::getInstance()->getReportFieldNames($report);
		
		$results = db_ReportManager::getInstance()->generateReport($report);
		
		return new template_admin_ReportResults($report, $fieldHeadings, $results);
	}
}

?>