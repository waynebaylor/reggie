<?php

class logic_admin_registration_RegOption extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function addRegOptions($params) { 
		foreach($params['regOpts'] as $optionId) { 
			db_reg_RegOptionManager::getInstance()->createOption($params['registrationId'], $optionId, $params['regOptPrice_'.$optionId]);
		}
		
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $params['registrationId']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'registration' => $registration
		);
	}
	
	public function cancelRegOption($params) {
		db_reg_RegOptionManager::getInstance()->cancel($params['id']);
				
		return $params;
	}
	
	public function saveVariableQuantity($params) {
		$currentOpts = db_reg_VariableQuantityManager::getInstance()->findByRegistration(array('id' => $params['registrationId']));
		
		foreach($params as $key => $value) {
			if(strpos($key, model_ContentType::$VAR_QUANTITY_OPTION.'_') === 0) {
				$optId = str_replace(model_ContentType::$VAR_QUANTITY_OPTION.'_', '', $key);
				$priceId = $params['priceId_'.$optId];
				
				$this->saveVariableQuantityOption($currentOpts, $params['registrationId'], $optId, $priceId, $value);
			}
		}
		
		return $params;
	}
	
	private function saveVariableQuantityOption($currentOpts, $registrationId, $optId, $priceId, $value) {
		// if the option already exists, then update it.
		foreach($currentOpts as $currentOpt) {
			if($currentOpt['variableQuantityId'] == $optId) {
				// only update if the price or quantity have changed.
				$optChanged = ($currentOpt['priceId'] != $priceId) || ($currentOpt['quantity'] != $value); 
				
				if($optChanged) {
					db_reg_VariableQuantityManager::getInstance()->save(array(
						'id' => $currentOpt['id'], 
						'priceId' => $priceId,
						'quantity' => $value
					));
				}
				return;
			}
		}

		// it's a new option, but only save if quantity is greater than 0.
		if($value > 0) {
			db_reg_VariableQuantityManager::getInstance()->createOption(array(
				'registrationId' => $registrationId, 
				'variableQuantityId' => $optId, 
				'priceId' => $priceId, 
				'quantity' => $value
			));
		}
	}
}

?>