<?php

class logic_admin_regOption_SectionRegOptionGroup extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$group = db_GroupManager::getInstance()->find($params);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		$bc = db_BreadcrumbManager::getInstance()->findSectionCrumbs($group['sectionId']);
			
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group,
			'breadcrumbsParams' => array(
				'eventId' => $bc['eventId'],
				'pageId' => $bc['pageId'],
				'sectionId' => $bc['sectionId'],
				'regGroupsAndOpts' => array(
					$params['id']
				)
			)
		);
	}
	
	public function addGroup($params) {
		db_GroupManager::getInstance()->createGroupUnderSection($params);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['sectionId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function removeGroup($params) {
		$group = db_GroupManager::getInstance()->find($params);
		
		db_GroupManager::getInstance()->deleteById($group);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $group['sectionId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveGroupUp($params) {
		$group = db_GroupManager::getInstance()->find($params);
		
		db_GroupManager::getInstance()->moveGroupUp($group);
		
		$section = db_PageSectionManager::getInstance()->find($group['sectionId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveGroupDown($params) {
		$group = db_GroupManager::getInstance()->find($params);
		
		db_GroupManager::getInstance()->moveGroupDown($group);
		
		$section = db_PageSectionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $group['sectionId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function saveGroup($params) {
		db_GroupManager::getInstance()->save($params);
		
		return array(
			'eventId' => $params['eventId'],
			'id' => $params['id']
		);
	}
}

?>