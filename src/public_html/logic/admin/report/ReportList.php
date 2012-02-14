<?php

class logic_admin_report_ReportList extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		$user = SessionUtil::getUser();
		$a = new action_admin_report_CreateReport();
		$showControls = $a->hasRole($user, $params['eventId']);
		
		return array(
			'eventId' => $eventInfo['id'],
			'actionMenuEventLabel' => $eventInfo['code'],
			'showControls' => $showControls? 'true' : 'false'
		);
	}
	
	public function listReports($params) {
		$reportInfos = db_ReportManager::getInstance()->findInfoByEventId($params);
		
		return array(
			'eventId' => $params['eventId'],
			'reports' => $reportInfos
		);
	}
	
	public function deleteReports($params) {
		db_ReportManager::getInstance()->deleteReports($params);
		
		return $params;
	}
}

?>