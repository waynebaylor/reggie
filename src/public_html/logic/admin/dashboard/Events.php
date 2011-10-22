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
		$regFormUrls = array();
		foreach($events as $event) {
			$hasEventRole = model_Role::userHasRoleForEvent($params['user'], model_Role::$EVENT_MANAGER, $event['id']);
			$canDelete[$event['id']] = $hasRole || $hasEventRole;
			
			$urls = array();
			$urls['attendeeUrl'] = db_EventManager::getInstance()->hasVisiblePages(array('eventId' => $event['id'], 'categoryId' => 1))?
				"/event/{$event['code']}/".model_Category::code(model_Category::valueOf(1)) : '';
			$urls['exhibitorUrl'] = db_EventManager::getInstance()->hasVisiblePages(array('eventId' => $event['id'], 'categoryId' => 2))?
				"/event/{$event['code']}/".model_Category::code(model_Category::valueOf(2)) : '';
			$urls['specialUrl'] = db_EventManager::getInstance()->hasVisiblePages(array('eventId' => $event['id'], 'categoryId' => 3))?
				"/event/{$event['code']}/".model_Category::code(model_Category::valueOf(3)) : '';

			$regFormUrls[$event['id']] = $urls;
		}
		
		return array(
			'user' => $params['user'],
			'events' => $events,
			'canDelete' => $canDelete,
			'regFormUrls' => $regFormUrls
		);
	}
}

?>