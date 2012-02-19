<?php

class logic_admin_section_Section extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$section = db_PageSectionManager::getInstance()->find($params);
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
		db_PageSectionManager::getInstance()->createSection($params);
	
		$page = db_PageManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['pageId']
		));
		
		return array(
			'eventId' => $params['eventId'],
			'page' => $page
		);
	}
	
	public function removeSection($params) {
		$section = db_PageSectionManager::getInstance()->find($params);

		db_PageSectionManager::getInstance()->delete($section);

		$page = db_PageManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $section['pageId']
		));

		return array(
			'eventId' => $params['eventId'],
			'page' => $page
		);
	}
	
	public function moveSectionUp($params) {
		$section = db_PageSectionManager::getInstance()->find($params);

		db_PageSectionManager::getInstance()->moveSectionUp($section);

		$page = db_PageManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $section['pageId']
		));

		return array(
			'eventId' => $params['eventId'],
			'page' => $page
		);
	}
	
	public function moveSectionDown($params) {
		$section = db_PageSectionManager::getInstance()->find($params);

		db_PageSectionManager::getInstance()->moveSectionDown($section);

		$page = db_PageManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $section['pageId']
		));

		return array(
			'eventId' => $params['eventId'],
			'page' => $page
		);
	}
}

?>