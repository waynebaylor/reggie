<?php

class logic_admin_registration_Registration extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function addRegistrantToGroup($regGroupId) {
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), $regGroupId);
		
		$r = reset($group['registrations']);
		
		$newReg = array(
			'regGroupId' => $regGroupId,
			'categoryId' => $r['categoryId'],
			'regTypeId' => $r['regTypeId'],
			'eventId' => $r['eventId'],
			'information' => array(),
			'regOptionIds' => array(),
			'variableQuantity' => array()
		);
		
		db_reg_RegistrationManager::getInstance()->createRegistration($regGroupId, $newReg);
		
		// return the updated group.
		return $this->strictFindById(db_reg_GroupManager::getInstance(), $regGroupId);
	}
	
	public function createNewRegistration($eventId, $categoryId) {
		$regGroupId = db_reg_GroupManager::getInstance()->createGroup();
		
		$regTypeId = 0;
		
		$regTypes = db_RegTypeManager::getInstance()->findByEvent(array('id' => $eventId));
		foreach($regTypes as $regType) {
			if(model_RegType::isVisibleTo($regType, array('id' => $categoryId))) {
				$regTypeId = $regType['id'];
				break;		
			}
		}

		$newReg = array(
			'regGroupId' => $regGroupId,
			'categoryId' => $categoryId,
			'regTypeId' => $regTypeId,
			'eventId' => $eventId,
			'information' => array(),
			'regOptionIds' => array(),
			'variableQuantity' => array()
		);
		
		db_reg_RegistrationManager::getInstance()->createRegistration($regGroupId, $newReg);
	}
}

?>