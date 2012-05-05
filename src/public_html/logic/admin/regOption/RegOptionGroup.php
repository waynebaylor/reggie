<?php

class logic_admin_regOption_RegOptionGroup extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$group = db_GroupManager::getInstance()->find($params);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group,
			'breadcrumbsParams' => db_BreadcrumbManager::getInstance()->findRegOptionGroupCrumbs(array(
				'eventId' => $params['eventId'],
				'regOptionGroupId' => $params['id']
			))
		);
	}
	
	public function addGroup($params) {
		$params['minimum'] = ($params['multiple'] === 'T')? $params['minimum'] : 0;
		$params['maximum'] = ($params['multiple'] === 'T')? $params['maximum'] : 0;
		
		db_GroupManager::getInstance()->createGroupUnderOption($params);
		
		$option = db_RegOptionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['regOptionId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function removeGroup($params) {
		$group = db_GroupManager::getInstance()->find($params);
		
		db_GroupManager::getInstance()->deleteById($group);
		
		$option = db_RegOptionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $group['regOptionId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function moveGroupUp($params) {
		$group = db_GroupManager::getInstance()->find($params);
		
		db_GroupManager::getInstance()->moveGroupUp($group);
		
		$option = db_RegOptionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $group['regOptionId']
		));
		
		$event = db_EventManager::getInstance()->find($group['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function moveGroupDown($params) {
		$group = db_GroupManager::getInstance()->find($params);
		
		db_GroupManager::getInstance()->moveGroupDown($group);
		
		$option = db_RegOptionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $group['regOptionId']
		));
		
		$event = db_EventManager::getInstance()->find($group['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function saveGroup($params) {
		db_GroupManager::getInstance()->save($params);
		
		return $params;
	}
}

?>