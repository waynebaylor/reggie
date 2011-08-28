<?php

class logic_admin_report_GenerateReport extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventId = $params['eventId'];
		$reportId = $params['reportId'];
		$searchTerm = isset($params['term'])? $params['term'] : '';
		$searchFieldId = isset($params['contactFieldId'])? $params['contactFieldId'] : 0;
		
		$event = db_EventManager::getInstance()->find($eventId);
		
		$report = db_ReportManager::getInstance()->findReport(array('eventId' => $eventId, 'id' => $reportId));
		$this->title = $report['reportName'];
		
		return array( 
			'actionMenuEventLabel' => $event['code'],
			'reportId' => $reportId,
			'eventId' => $eventId,
			'event' => $event,
			'isSearch' => 'false',
			'showSearchLink' => model_Report::hasSearch($report)? 'true' : 'false',
			'searchTerm' => $searchTerm,
			'searchFieldId' => $searchFieldId
		);
	}
	
	public function csv($params) {
		return $this->view($params);
	}
	
	public function search($params) {
		$field = db_ContactFieldManager::getInstance()->find($params['contactFieldId']);
		
		$info = $this->view($params);
		
		$info['isSearch'] = true;
		$info['searchTerm'] = $params['term'];
		$info['searchField'] = $field['displayName'];
		
		return $info;
	}
}

?>