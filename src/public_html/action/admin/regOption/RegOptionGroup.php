<?php

class action_admin_regOption_RegOptionGroup extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$group = $this->strictFindById(db_GroupManager::getInstance(), RequestUtil::getValue('id', 0));
		
		$event = db_EventManager::getInstance()->find($group['eventId']);
		
		return new template_admin_EditSectionRegOptionGroup($event, $group);
	}
	
	public function addGroup() {
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), RequestUtil::getValue('regOptionId', 0));
		
		$required = RequestUtil::getValue('required', 'F');
		$multiple = RequestUtil::getValue('multiple', 'F');

		$minimum = ($multiple === 'T')? $_REQUEST['minimum'] : 0;
		$maximum = ($multiple === 'T')? $_REQUEST['maximum'] : 0;
		
		$group = array(
			'eventId' => RequestUtil::getValue('eventId', 0),
			'regOptionId' => $option['id'],
			'required' => $required,
			'multiple' => $multiple,
			'minimum' => $minimum,
			'maximum' => $maximum
		);
		
		db_GroupManager::getInstance()->createGroupUnderOption($group);
		
		$option = db_RegOptionManager::getInstance()->find($option['id']);
		$event = db_EventManager::getInstance()->find($group['eventId']);
		
		return new fragment_regOptionGroup_List($event, $option);
	}
	
	public function removeGroup() {
		$group = $this->strictFindById(db_GroupManager::getInstance(), RequestUtil::getValue('id', 0));
		
		db_GroupManager::getInstance()->deleteById($group['id']);
		
		$option = db_RegOptionManager::getInstance()->find($group['regOptionId']);
		$event = db_EventManager::getInstance()->find($group['eventId']);
		
		return new fragment_regOptionGroup_List($event, $option);
	}
	
	public function moveGroupUp() {
		$group = $this->strictFindById(db_GroupManager::getInstance(), RequestUtil::getValue('id', 0));
		
		db_GroupManager::getInstance()->moveGroupUp($group);
		
		$option = db_RegOptionManager::getInstance()->find($group['regOptionId']);
		$event = db_EventManager::getInstance()->find($group['eventId']);
		
		return new fragment_regOptionGroup_List($event, $option);
	}
	
	public function moveGroupDown() {
		$group = $this->strictFindById(db_GroupManager::getInstance(), RequestUtil::getValue('id', 0));
		
		db_GroupManager::getInstance()->moveGroupDown($group);
		
		$option = db_RegOptionManager::getInstance()->find($group['regOptionId']);
		$event = db_EventManager::getInstance()->find($group['eventId']);
		
		return new fragment_regOptionGroup_List($event, $option);
	}
	
	public function saveGroup() {
		$group = $this->strictFindById(db_GroupManager::getInstance(), RequestUtil::getValue('id', 0));
		
		$group = array(
			'id' => $_REQUEST['id'],
			'required' => RequestUtil::getValue('required', 'F'),
			'multiple' => RequestUtil::getValue('multiple', 'F'),
			'minimum' => RequestUtil::getValue('minimum', 0),
			'maximum' => RequestUtil::getValue('maximum', 0)
		);
		
		db_GroupManager::getInstance()->save($group);
		
		return new fragment_Success();
	}
}

?>