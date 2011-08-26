<?php

class logic_admin_badge_CreateBadgeTemplate extends logic_Performer
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
	
	public function createTemplate($params) {
		db_BadgeTemplateManager::getInstance()->createBadgeTemplate($params);
		
		return array('eventId' => $params['eventId']);
	}
}

?>