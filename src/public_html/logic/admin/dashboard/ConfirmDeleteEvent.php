<?php

class logic_admin_dashboard_ConfirmDeleteEvent extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($user, $eventId) {
		return array(
			'user' => $user,
			'eventId' => $eventId
		);
	}
	
	public function deleteEvent($user, $eventId) {
		if(SecurityUtil::hasEventPermission($user, $eventId)) {
			db_EventManager::getInstance()->delete($eventId);
		}
		
		return array();
	}
}

?>