<?php

class logic_admin_regOption_RegOption extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), $params['id']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option,
			'breadcrumbsParams' => $this->breadcrumbsParams($params['id'])
		);
	}
	
	public function addOption($params) {
		$newOptionId = db_RegOptionManager::getInstance()->createRegOption($params);
		
		// create default $0 price for new option.
		db_RegOptionPriceManager::getInstance()->createRegOptionPrice(array(
			'eventId' => $params['eventId'],
			'regOptionId' => $newOptionId,
			'description' => 'free',
			'startDate' => date(db_Manager::$DATE_FORMAT),
			'endDate' => date(db_Manager::$DATE_FORMAT, time()+604800),
			'price' => '0.00',
			'regTypeIds' => array(-1)
		));
		
		$group = db_GroupManager::getInstance()->find($params['parentGroupId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group
		);
	}
	
	public function removeOption($params) {
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), $params['id']);
		
		db_RegOptionManager::getInstance()->delete($option);
		
		$group = db_GroupManager::getInstance()->find($option['parentGroupId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group
		);
	}
	
	public function moveOptionUp($params) {
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), $params['id']);
		
		db_RegOptionManager::getInstance()->moveOptionUp($option);
		
		$group = db_GroupManager::getInstance()->find($option['parentGroupId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group
		);
	}
	
	public function moveOptionDown($params) {
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), $params['id']);
		
		db_RegOptionManager::getInstance()->moveOptionDown($option);
		
		$group = db_GroupManager::getInstance()->find($option['parentGroupId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group
		);
	}
	
	public function saveOption($params) {
		db_RegOptionManager::getInstance()->save($params);
		
		return $params;
	}
	
	private function breadcrumbsParams($regOptionId) {
		$groupsAndOpts = db_BreadcrumbManager::getInstance()->getGroupsAndOpts($regOptionId);
		
		// the first id in $groupsAndOpts is the section group.
		$group = db_GroupManager::getInstance()->find($groupsAndOpts[0]);
		$bc = db_BreadcrumbManager::getInstance()->findSectionCrumbs($group['sectionId']);
			
		return array(
			'eventId' => $bc['eventId'],
			'pageId' => $bc['pageId'],
			'sectionId' => $bc['sectionId'],
			'regGroupsAndOpts' => $groupsAndOpts
		);
	}
	
	
}

?>