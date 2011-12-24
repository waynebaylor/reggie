<?php

class logic_admin_regType_RegType extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $params['id']);
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
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $params['id']);
		
		$regType['description'] = $params['description'];
		$regType['code'] = $params['code'];
		$categoryIds = $params['categoryIds'];

		db_RegTypeManager::getInstance()->save($regType, $categoryIds);
		
		return $params;
	}
	
	public function addRegType($params) {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $params['sectionId']);

		db_RegTypeManager::getInstance()->createRegType(
			$params['eventId'], $section['id'], $params['description'], $params['code'], $params['categoryIds']);

		$event = db_EventManager::getInstance()->find($params['eventId']);
		$section = db_PageSectionManager::getInstance()->find($section['id']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function removeRegType($params) {
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $params['id']);

		db_RegTypeManager::getInstance()->delete($regType);

		$event = db_EventManager::getInstance()->find($params['eventId']);
		$section = db_PageSectionManager::getInstance()->find($regType['sectionId']);

		return array(
			'eventId' => $event['id'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveRegTypeUp($params) {
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $params['id']);

		db_RegTypeManager::getInstance()->moveRegTypeUp($regType);

		$event = db_EventManager::getInstance()->find($params['eventId']);
		$section = db_PageSectionManager::getInstance()->find($regType['sectionId']);

		return array(
			'eventId' => $event['id'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveRegTypeDown($params) {
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $params['id']);
		
		db_RegTypeManager::getInstance()->moveRegTypeDown($regType);

		$event = db_EventManager::getInstance()->find($params['eventId']);
		$section = db_PageSectionManager::getInstance()->find($regType['sectionId']);

		return array(
			'eventId' => $event['id'],
			'event' => $event,
			'section' => $section
		);
	}
}

?>