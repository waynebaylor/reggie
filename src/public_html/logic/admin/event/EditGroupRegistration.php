<?php

class logic_admin_event_EditGroupRegistration extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event
		);
	}
	
	public function saveGroupReg($params) {
		db_GroupRegistrationManager::getInstance()->save($params);
		
		return array('eventId' => $params['eventId']);
	}
}

?>