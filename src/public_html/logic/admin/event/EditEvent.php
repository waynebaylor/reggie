<?php

class logic_admin_event_EditEvent extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $event['id'],
			'event' => $event,
			'showTab' => $params['showTab'],
			'breadcrumbsParams' => array(
				'eventId' => $event['id']
			) 
		);
	}
	
	public function saveEvent($params) {
		$oldEvent = $this->strictFindById(db_EventManager::getInstance(), $params['id']);
	
		db_EventManager::getInstance()->save($params);
		
		FileUtil::renameEventDir($oldEvent, $params);
		
		return $params;
	}
	
	public function addPage($params) {
		db_PageManager::getInstance()->createPage($params);

		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event
		); 
	}
	
	public function removePage($params) {
		$page = db_PageManager::getInstance()->find($params);

		db_PageManager::getInstance()->deletePage($page);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event
		);
	}
	
	public function movePageUp($params) {
		db_PageManager::getInstance()->movePageUp($params);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event
		);
	}
	
	public function movePageDown($params) {
		db_PageManager::getInstance()->movePageDown($params);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => 0,
			'event' => $event
		);
	}
}
		
?>