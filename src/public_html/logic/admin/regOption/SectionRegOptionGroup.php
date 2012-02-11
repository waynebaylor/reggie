<?php

class logic_admin_regOption_SectionRegOptionGroup extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$group = $this->strictFindById(db_GroupManager::getInstance(), $params['id']);
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
		
		$section = db_PageSectionManager::getInstance()->find($params['sectionId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function removeGroup($params) {
		$group = $this->strictFindById(db_GroupManager::getInstance(), $params['id']);
		
		db_GroupManager::getInstance()->deleteById($group['id']);
		
		$section = db_PageSectionManager::getInstance()->find($group['sectionId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'section' => $section
		);
	}
	
	public function moveGroupUp($params) {
		$group = $this->strictFindById(db_GroupManager::getInstance(), $params['id']);
		
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
		$group = $this->strictFindById(db_GroupManager::getInstance(), $params['id']);
		
		db_GroupManager::getInstance()->moveGroupDown($group);
		
		$section = db_PageSectionManager::getInstance()->find($group['sectionId']);
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