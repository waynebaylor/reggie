<?php

class logic_admin_regOption_VariableQuantity extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$option = db_VariableQuantityOptionManager::getInstance()->find($params);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option,
			'breadcrumbsParams' => db_BreadcrumbManager::getInstance()->findVariableRegOptionCrumbs(array(
				'eventId' => $params['eventId'],
				'id' => $params['id']
			))
		);
	}
	
	public function addOption($params) {
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['sectionId']
		));
		
		$newVarOptId = db_VariableQuantityOptionManager::getInstance()->createOption($params);
		
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		// create default $0 price for new option.
		db_RegOptionPriceManager::getInstance()->createVariableQuantityPrice(array(
			'eventId' => $params['eventId'],
			'regOptionId' => $newVarOptId,
			'description' => 'free',
			'startDate' => $eventInfo['regOpen'],
			'endDate' => $eventInfo['regClosed'],
			'price' => '0.00',
			'regTypeIds' => array(-1)
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $section['id']
		));

		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function removeOption($params) {
		$option = db_VariableQuantityOptionManager::getInstance()->find($params);
		
		db_VariableQuantityOptionManager::getInstance()->delete($option);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $option['sectionId']
		));

		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function saveOption($params) {
		db_VariableQuantityOptionManager::getInstance()->save($params);
		
		return $params;
	}
	
	public function moveOptionUp($params) {
		$option = db_VariableQuantityOptionManager::getInstance()->find($params);
		
		db_VariableQuantityOptionManager::getInstance()->moveOptionUp($option);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $option['sectionId']
		));

		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveOptionDown($params) {
		$option = db_VariableQuantityOptionManager::getInstance()->find($params);
		
		db_VariableQuantityOptionManager::getInstance()->moveOptionDown($option);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $option['sectionId']
		));

		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
}

?>