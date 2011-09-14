<?php

class logic_admin_event_CreateEvent extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'user' => $params['user'],
			'event' => array(
				'id' => 0,
				'paymentInstructions' => '',
				'code' => '',
				'displayName' => '',
				'regOpen' => '',
				'regClosed' => '',
				'capacity' => '',
				'confirmationText' => '',
				'regClosedText' => '',
				'cancellationPolicy' => ''
			)
		);
	}
	
	public function createEvent($params) {
		$newEventId = db_EventManager::getInstance()->createEvent($params['event']);
		
		$event = db_EventManager::getInstance()->find($newEventId);
		
		FileUtil::createEventDir($event);
		
		if(!model_Role::userHasRole($params['user'], array(model_Role::$SYSTEM_ADMIN, model_Role::$EVENT_ADMIN))) {
			db_UserManager::getInstance()->assignUserEventRole(
				$params['user']['id'], 
				model_Role::$EVENT_MANAGER, 
				$newEventId
			);
		}
		
		// Refresh the session user because their event permissions
		// have changed.
		SessionUtil::refreshUser();
		
		return array(
			'user' => $params['user'],
			'newEventId' => $newEventId
		);
	}
}

?>