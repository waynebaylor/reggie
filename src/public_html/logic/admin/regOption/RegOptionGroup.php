<?php

class logic_admin_regOption_RegOptionGroup extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$group = $this->strictFindById(db_GroupManager::getInstance(), $params['id']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group,
			'breadcrumbsParams' => $this->breadcrumbsParams($group)
		);
		
		return new template_admin_EditSectionRegOptionGroup($event, $group);
	}
	
	private function breadcrumbsParams($group) {
		$groupsAndOpts = db_BreadcrumbManager::getInstance()->getGroupsAndOpts($group['regOptionId']);
		$groupsAndOpts[] = $group['id'];
		
		// the first id in $groupsAndOpts is the section group.
		$sectionGroup = db_GroupManager::getInstance()->find($groupsAndOpts[0]);
		$bc = db_BreadcrumbManager::getInstance()->findSectionCrumbs($sectionGroup['sectionId']);
			
		return array(
			'eventId' => $bc['eventId'],
			'pageId' => $bc['pageId'],
			'sectionId' => $bc['sectionId'],
			'regGroupsAndOpts' => $groupsAndOpts
		);
	}
	
	public function addGroup($params) {
		$params['minimum'] = ($params['multiple'] === 'T')? $params['minimum'] : 0;
		$params['maximum'] = ($params['multiple'] === 'T')? $params['maximum'] : 0;
		
		db_GroupManager::getInstance()->createGroupUnderOption($params);
		
		$option = db_RegOptionManager::getInstance()->find($params['regOptionId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function removeGroup($params) {
		$group = $this->strictFindById(db_GroupManager::getInstance(), $params['id']);
		
		db_GroupManager::getInstance()->deleteById($group['id']);
		
		$option = db_RegOptionManager::getInstance()->find($group['regOptionId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function moveGroupUp($params) {
		$group = $this->strictFindById(db_GroupManager::getInstance(), $params['id']);
		
		db_GroupManager::getInstance()->moveGroupUp($group);
		
		$option = db_RegOptionManager::getInstance()->find($group['regOptionId']);
		$event = db_EventManager::getInstance()->find($group['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function moveGroupDown($params) {
		$group = $this->strictFindById(db_GroupManager::getInstance(), $params['id']);
		
		db_GroupManager::getInstance()->moveGroupDown($group);
		
		$option = db_RegOptionManager::getInstance()->find($group['regOptionId']);
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