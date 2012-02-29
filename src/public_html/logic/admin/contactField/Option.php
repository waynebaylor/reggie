<?php

class logic_admin_contactField_Option extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$option = db_ContactFieldOptionManager::getInstance()->find($params['id']);
		$event = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		$bc = db_BreadcrumbManager::getInstance()->findContactFieldCrumbs($option['contactFieldId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option,
			'breadcrumbsParams' => array(
				'eventId' => $bc['eventId'],
				'pageId' => $bc['pageId'],
				'sectionId' => $bc['sectionId'],
				'contactFieldId' => $bc['contactFieldId'],
				'contactFieldOptionId' => $option['id']
			)
		);
	}
	
	public function addOption($params) {
		db_ContactFieldOptionManager::getInstance()->createOption($params);
		
		$field = db_ContactFieldManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['contactFieldId']
		));
		
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

		$field = db_ContactFieldManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $option['contactFieldId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'field' => $field
		);
	}
	
	public function saveOption($params) {
		db_ContactFieldOptionManager::getInstance()->save($params);

		return $params;
	}
	
	public function moveOptionUp($params) {
		$option = $this->strictFindById(db_ContactFieldOptionManager::getInstance(), $params['id']);
		
		db_ContactFieldOptionManager::getInstance()->moveOptionUp($option);
		
		$field = db_ContactFieldManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $option['contactFieldId']
		));
		
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
		
		$field = db_ContactFieldManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $option['contactFieldId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'field' => $field
		);
	}
}

?>