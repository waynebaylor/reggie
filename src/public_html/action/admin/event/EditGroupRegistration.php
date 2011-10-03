<?php

class action_admin_event_EditGroupRegistration extends action_BaseAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_EditGroupRegistration();
		$this->converter = new viewConverter_admin_event_EditGroupRegistration();
	}
	
	public static function checkRole($user, $eventId=0, $method='') {
		return action_admin_event_EditEvent::checkRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));
			
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function saveGroupReg() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0,
			'enabled' => 'F',
			'defaultRegType' => 'F'
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->saveGroupReg($params);
		return $this->converter->getSaveGroupReg($info);
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