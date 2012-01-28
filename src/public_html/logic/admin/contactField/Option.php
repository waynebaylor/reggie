<?php

class logic_admin_contactField_Option extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function addOption($params) {
		db_ContactFieldOptionManager::getInstance()->createOption($params);
		
		$field = db_ContactFieldManager::getInstance()->find($params['contactFieldId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'field' => $field
		);
	}
	
	public function removeOption($params) {
		$option = $this->strictFindById(db_ContactFieldOptionManager::getInstance(), $params['id']);

		db_ContactFieldOptionManager::getInstance()->delete($option);

		$field = db_ContactFieldManager::getInstance()->find($option['contactFieldId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'field' => $field
		);
	}
	
	public function moveOptionUp($params) {
		$option = $this->strictFindById(db_ContactFieldOptionManager::getInstance(), $params['id']);
		
		db_ContactFieldOptionManager::getInstance()->moveOptionUp($option);
		
		$field = db_ContactFieldManager::getInstance()->find($option['contactFieldId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'field' => $field
		);
	}
	
	public function moveOptionDown($params) {
		$option = $this->strictFindById(db_ContactFieldOptionManager::getInstance(), $params['id']);
		
		db_ContactFieldOptionManager::getInstance()->moveOptionDown($option);
		
		$field = db_ContactFieldManager::getInstance()->find($option['contactFieldId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'field' => $field
		);
	}
}

?>