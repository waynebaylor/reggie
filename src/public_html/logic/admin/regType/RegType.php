<?php

class logic_admin_regType_RegType extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$regType = db_RegTypeManager::getInstance()->find($params);
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		
		$bc = db_BreadcrumbManager::getInstance()->findRegTypeCrumbs($params['id']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'regType' => $regType,
			'event' => $event,
			'breadcrumbsParams' => array(
				'eventId' => $bc['eventId'],
				'pageId' => $bc['pageId'],
				'sectionId' => $bc['sectionId'],
				'regTypeId' => $bc['regTypeId']
			)
		);
	}
	
	public function saveRegType($params) {
		db_RegTypeManager::getInstance()->save($params);
		
		return $params;
	}
	
	public function addRegType($params) {
		db_RegTypeManager::getInstance()->createRegType(array(
			'eventId' => $params['eventId'], 
			'sectionId' => $params['sectionId'], 
			'description' => $params['description'], 
			'code' => $params['code'], 
			'categoryIds' => $params['categoryIds']
		));

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
	
	public function removeRegType($params) {
		$regType = db_RegTypeManager::getInstance()->find($params);

		db_RegTypeManager::getInstance()->delete(array(
			'eventId' => $params['eventId'],
			'regTypeId' => $regType['id']
		));

		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $regType['sectionId']
		));

		return array(
			'eventId' => $event['id'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveRegTypeUp($params) {
		$regType = db_RegTypeManager::getInstance()->find($params);

		db_RegTypeManager::getInstance()->moveRegTypeUp($regType);

		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $regType['sectionId']
		));

		return array(
			'eventId' => $event['id'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveRegTypeDown($params) {
		$regType = db_RegTypeManager::getInstance()->find($params);
		
		db_RegTypeManager::getInstance()->moveRegTypeDown($regType);

		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $regType['sectionId']
		));

		return array(
			'eventId' => $event['id'],
			'event' => $event,
			'section' => $section
		);
	}
}

?>