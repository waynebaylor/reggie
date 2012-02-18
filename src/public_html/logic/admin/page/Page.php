<?php

class logic_admin_page_Page extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$page = db_PageManager::getInstance()->find($params);
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $event['id'],
			'event' => $event,
			'page' => $page,
			'breadcrumbsParams' => array(
				'eventId' => $event['id'],
				'pageId' => $page['id']
			)
		);
	}
	
	public function savePage($params) {
		db_PageManager::getInstance()->savePage($params);
		
		return $params;
	}
}

?>