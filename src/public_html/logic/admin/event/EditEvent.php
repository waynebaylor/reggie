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
			'event' => $event
		);
	}
	
	public function saveEvent($info) {
		$oldEvent = $this->strictFindById(db_EventManager::getInstance(), $info['id']);
	
		db_EventManager::getInstance()->save($info);
		
		FileUtil::renameEventDir($oldEvent, $info);
	}
	
	public function addPage($eventId, $title, $categoryIds) {
		db_PageManager::getInstance()->createPage($eventId, $title, $categoryIds);

		return db_EventManager::getInstance()->find($eventId);
	}
	
	public function removePage($pageId) {
		$page = $this->strictFindById(db_PageManager::getInstance(), $pageId);

		db_PageManager::getInstance()->deletePage($page);
		
		return db_EventManager::getInstance()->find($page['eventId']);
	}
	
	public function movePageUp($pageId) {
		$page = $this->strictFindById(db_PageManager::getInstance(), $pageId);

		db_PageManager::getInstance()->movePageUp($page);
		
		return db_EventManager::getInstance()->find($page['eventId']);
	}
	
	public function movePageDown($pageId) {
		$page = $this->strictFindById(db_PageManager::getInstance(), $pageId);

		db_PageManager::getInstance()->movePageDown($page);
		
		return db_EventManager::getInstance()->find($page['eventId']);
	}
}
		
?>