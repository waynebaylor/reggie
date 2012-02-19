<?php

class logic_admin_contactField_ContactField extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $params['id']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		
		$bc = db_BreadcrumbManager::getInstance()->findContactFieldCrumbs($params['id']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'field' => $field,
			'event' => $event,
			'breadcrumbsParams' => array(
				'eventId' => $bc['eventId'],
				'pageId' => $bc['pageId'],
				'sectionId' => $bc['sectionId'],
				'contactFieldId' => $bc['contactFieldId']
			)
		);
	}
	
	public function addField($params) {
		$field = array();
		ObjectUtils::populate($field, $params['request']); 
		$field['eventId'] = $params['eventId'];
		
		db_ContactFieldManager::getInstance()->createContactField($field);

		$event = db_EventManager::getInstance()->find($params['eventId']);
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['sectionId']
		));
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function removeField($params) {
		$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $params['id']);

		$sectionId = $field['sectionId'];
		
		db_ContactFieldManager::getInstance()->delete($field);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $sectionId
		));
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveFieldUp($params) {
		$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $params['id']);
		
		db_ContactFieldManager::getInstance()->moveFieldUp($field);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $field['sectionId']
		));
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveFieldDown($params) {
		$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $params['id']);
		
		db_ContactFieldManager::getInstance()->moveFieldDown($field);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $field['sectionId']
		));
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function save($params) {
		$field = array();
		ObjectUtils::populate($field, $params['request']);

		db_ContactFieldManager::getInstance()->save($field);
	
		return array(
			'eventId' => $params['eventId']
		);
	}
}

?>