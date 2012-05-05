<?php

class logic_admin_regOption_RegOption extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$option = db_RegOptionManager::getInstance()->find($params);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option,
			'breadcrumbsParams' => db_BreadcrumbManager::getInstance()->findRegOptionCrumbs(array(
				'eventId' => $params['eventId'],
				'regOptionId' => $params['id']
			))
		);
	}
	
	public function addOption($params) {
		$newOptionId = db_RegOptionManager::getInstance()->createRegOption($params);
		
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		// create default $0 price for new option.
		db_RegOptionPriceManager::getInstance()->createRegOptionPrice(array(
			'eventId' => $params['eventId'],
			'regOptionId' => $newOptionId,
			'description' => 'free',
			'startDate' => $eventInfo['regOpen'],
			'endDate' => $eventInfo['regClosed'],
			'price' => '0.00',
			'regTypeIds' => array(-1)
		));
		
		$group = db_GroupManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['parentGroupId']
		));
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group
		);
	}
	
	public function removeOption($params) {
		$option = db_RegOptionManager::getInstance()->find($params);
		
		db_RegOptionManager::getInstance()->delete($option);
		
		$group = db_GroupManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $option['parentGroupId']
		));
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group
		);
	}
	
	public function moveOptionUp($params) {
		$option = db_RegOptionManager::getInstance()->find($params);
		
		db_RegOptionManager::getInstance()->moveOptionUp($option);
		
		$group = db_GroupManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $option['parentGroupId']
		));
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group
		);
	}
	
	public function moveOptionDown($params) {
		$option = db_RegOptionManager::getInstance()->find($params);
		
		db_RegOptionManager::getInstance()->moveOptionDown($option);
		
		$group = db_GroupManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $option['parentGroupId']
		));
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group
		);
	}
	
	public function saveOption($params) {
		if($params['isText'] === 'true') {
			db_RegOptionManager::getInstance()->saveText($params);
		}
		else {
			db_RegOptionManager::getInstance()->save($params);
		}
		
		return $params;
	}
	
	public function addText($params) {
		$newOptionId = db_RegOptionManager::getInstance()->createText($params);
		
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		$group = db_GroupManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['parentGroupId']
		));
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group
		);
	}
}

?>