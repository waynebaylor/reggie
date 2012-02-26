<?php

class logic_admin_registration_CreateRegistration extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $eventInfo['code'],
			'eventId' => $eventInfo['id']
		);
	}
	
	public function createRegistration($params) {
		$regGroupId = db_reg_GroupManager::getInstance()->createGroup();
		
		$regType = db_RegTypeManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['regTypeId']
		));
		
		$category = reset($regType['visibleTo']);
		
		$newReg = array(
			'regGroupId' => $regGroupId,
			'categoryId' => $category['id'],
			'regTypeId' => $regType['id'],
			'eventId' => $params['eventId'],
			'information' => array(),
			'regOptionIds' => array(),
			'variableQuantity' => array()
		);
		
		$newRegId = db_reg_RegistrationManager::getInstance()->createRegistration($regGroupId, $newReg);
		
		db_reg_RegistrationManager::getInstance()->createLeadNumber($params['eventId'], $newRegId);
		
		return array(
			'eventId' => $params['eventId'],
			'regGroupId' => $regGroupId,
			'registrationId' => $newRegId
		);
	}
}

?>