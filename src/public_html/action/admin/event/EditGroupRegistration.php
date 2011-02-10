<?php

class action_admin_event_EditGroupRegistration extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$id = RequestUtil::getValue('id', 0);
		$event = $this->strictFindById(db_EventManager::getInstance(), $id);
		
		return new template_admin_EditGroupRegistration($event);		
	}
	
	public function saveGroupReg() {
		$groupReg = RequestUtil::getParameters(array(
			'id',
			'eventId'
		));
		
		$groupReg['enabled'] = RequestUtil::getValue('enabled', 'F');
		$groupReg['defaultRegType'] = RequestUtil::getValue('defaultRegType', 'F');
		
		db_GroupRegistrationManager::getInstance()->save($groupReg);
		
		return new fragment_Success();
	}
	
	public function addField() {
		$field = RequestUtil::getParameters(array('groupRegistrationId', 'contactFieldId'));
		
		db_GroupRegistrationFieldManager::getInstance()->createField($field);

		$groupReg = $this->strictFindById(db_GroupRegistrationManager::getInstance(), $field['groupRegistrationId']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $groupReg['eventId']);
		
		return new fragment_groupRegistration_field_List($event);
	}
	
	public function removeField() {
		$field = $this->strictFindById(db_GroupRegistrationFieldManager::getInstance(), RequestUtil::getValue('id', 0));
		
		db_GroupRegistrationFieldManager::getInstance()->deleteField($field);
		
		$groupReg = $this->strictFindById(db_GroupRegistrationManager::getInstance(), $field['groupRegistrationId']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $groupReg['eventId']);
		
		return new fragment_groupRegistration_field_List($event);
	}
}

?>