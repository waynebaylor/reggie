<?php

class logic_admin_dashboard_ConfirmDeleteEvent extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'eventIds' => $params['eventIds']
		);
	}
	
	public function deleteEvents($params) {
		foreach($params['eventIds'] as $eventId) {
			$eventInfo = db_EventManager::getInstance()->findInfoById($eventId);
			FileUtil::deleteEventDir($eventInfo);
			
			db_EventManager::getInstance()->delete($eventId);
		}
		
		return array();
	}
}

?>