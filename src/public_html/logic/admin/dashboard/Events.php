<?php

class logic_admin_dashboard_Events extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'user' => $params['user']
		);
	}
	
	public function listEvents($params) {
		$events = db_EventManager::getInstance()->findInfoByUserId($params['user']['id']);
		
		$hasRole = model_Role::userHasRole($params['user'], array(model_Role::$SYSTEM_ADMIN, model_Role::$EVENT_ADMIN));
		$canDelete = array();
		foreach($events as $event) {
			$hasEventRole = model_Role::userHasRoleForEvent($params['user'], model_Role::$EVENT_MANAGER, $event['id']);
			$canDelete[$event['id']] = $hasRole || $hasEventRole;
		}
		
		return array(
			'user' => $params['user'],
			'events' => $events,
			'canDelete' => $canDelete
		);
	}
}

?>