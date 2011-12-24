<?php

class logic_admin_section_Section extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $section['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $event['id'],
			'event' => $event,
			'section' => $section,
			'breadcrumbsParams' => array(
				'eventId' => $event['id'],
				'pageId' => $section['pageId'],
				'sectionId' => $section['id']
			)
		);
	}
	
	public function saveSection($params) {
		db_PageSectionManager::getInstance()->save($params);
		
		return $params;
	}
	
	public function addSection($params) {
		db_PageSectionManager::getInstance()->createSection($params['eventId'], $params['pageId'], $params['name'], $params['contentTypeId']);
	
		$page = db_PageManager::getInstance()->find($params['pageId']);
		
		return array(
			'eventId' => $params['eventId'],
			'page' => $page
		);
	}
}

?>