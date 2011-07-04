<?php

class logic_admin_report_Reports extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($eventId) {
		$event = $this->strictFindById(db_EventManager::getInstance(), $eventId);
		
		$searchFormInfo = array(
			'reportId' => 0, // this is set dynamically with javascript.
			'eventId' => $event['id'],
			'event' => $event
		);
		
		return array(
			'title' => 'Reports',
			'event' => $event,
			'searchFormInfo' => $searchFormInfo
		);
	}
	
	public function addReport($eventId, $reportName) {
		$event = $this->strictFindById(db_EventManager::getInstance(), $eventId);
		
		db_ReportManager::getInstance()->createReport(array(
			'eventId' => $event['id'],
			'name' => $reportName
		));
		
		return $this->view($event['id']);
	}
	
	public function removeReport($reportId) {
		$report = $this->strictFindById(db_ReportManager::getInstance(), $reportId);
		
		db_ReportManager::getInstance()->deleteReport($report);
		
		return $this->view($report['eventId']);
	}
}

?>