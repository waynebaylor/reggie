<?php

class logic_admin_report_ReportList extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		return array(
			'eventId' => $eventInfo['id'],
			'actionMenuEventLabel' => $eventInfo['code']
		);
	}
	
	public function listReports($params) {
		$reportInfos = db_ReportManager::getInstance()->findInfoByEventId($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'reports' => $reportInfos
		);
	}
	
	public function deleteReports($params) {
		db_ReportManager::getInstance()->deleteReports(array(
			'eventId' => $params['eventId'],
			'reportIds' => $params['reportIds']
		));
		
		return array('eventId' => $params['eventId']);
	}
}

?>