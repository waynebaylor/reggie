<?php

class logic_admin_page_Page extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$page = $this->strictFindById(db_PageManager::getInstance(), $params['id']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $event['id'],
			'event' => $event,
			'page' => $page
		);
	}
	
	public function savePage($params) {
		$page = $this->strictFindById(db_PageManager::getInstance(), $params['id']);
		
		$page['title'] = $params['title'];
		$categoryIds = $params['categoryIds'];
		
		db_PageManager::getInstance()->savePage($page, $categoryIds);
		
		return array();
	}
}

?>