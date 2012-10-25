<?php

class logic_admin_report_CreateReport extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $eventInfo['code'],
			'eventId' => $eventInfo['id'],
			'report' => array(
				'id' => 0,
				'name' => '',
				'type' => model_Report::$STANDARD,
				'showDateRegistered' => 'F',
				'showDateCancelled' => 'F',
				'showCategory' => 'F',
				'showRegType' => 'F',
				'showTotalCost' => 'F',
				'showTotalPaid' => 'F',
				'showRemainingBalance' => 'F',
				'fields' => array()
			)
		);
	}
	
	public function createReport($params) {
		$newId = db_ReportManager::getInstance()->createReport($params);
		
		return array(
			'eventId' => $params['eventId'],
			'reportId' => $newId
		);
	}
}

?>