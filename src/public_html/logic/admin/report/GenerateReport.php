<?php

class logic_admin_report_GenerateReport extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventId = $params['eventId'];
		$reportId = $params['reportId'];
		
		$eventInfo = db_EventManager::getInstance()->findInfoById($eventId);
		
		$report = db_ReportManager::getInstance()->findReport(array('eventId' => $eventId, 'id' => $reportId));
		
		return array( 
			'actionMenuEventLabel' => $eventInfo['code'],
			'reportId' => $reportId,
			'eventId' => $eventId,
			'report' => $report
		);
	}
	
	public function csv($params) {
		$resultsLogic = new logic_admin_report_Results();
		return $resultsLogic->view($params);
	}
}

?>