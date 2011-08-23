<?php

class logic_admin_report_CreateReport extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'eventId' => $params['eventId'],
			'report' => array(
				'id' => 0,
				'name' => '',
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
		db_ReportManager::getInstance()->createReport($params);
		
		return array('eventId' => $params['eventId']);
	}
}

?>