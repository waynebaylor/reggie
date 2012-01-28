<?php

class logic_admin_event_EditGroupRegistration extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event
		);
	}
	
	public function saveGroupReg($params) {
		db_GroupRegistrationManager::getInstance()->save($params);
		
		return array('eventId' => $params['eventId']);
	}
	
	public function addField($params) {
		db_GroupRegistrationFieldManager::getInstance()->createField($params);

		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event
		);
	}
	
	public function removeField($params) {
		$field = $this->strictFindById(db_GroupRegistrationFieldManager::getInstance(), $params['id']);
		
		db_GroupRegistrationFieldManager::getInstance()->deleteField($field);
		
		$groupReg = $this->strictFindById(db_GroupRegistrationManager::getInstance(), $field['groupRegistrationId']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $groupReg['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event
		);
	}
}

?>