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
		
		$regTypeId = 0;
		
		$regTypes = db_RegTypeManager::getInstance()->findByEvent(array('id' => $params['eventId']));
		foreach($regTypes as $regType) {
			if(model_RegType::isVisibleTo($regType, array('id' => $params['categoryId']))) {
				$regTypeId = $regType['id'];
				break;		
			}
		}

		$newReg = array(
			'regGroupId' => $regGroupId,
			'categoryId' => $params['categoryId'],
			'regTypeId' => $regTypeId,
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