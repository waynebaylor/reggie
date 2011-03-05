<?php

class action_admin_registration_RegOption extends action_ValidatorAction
{
	public function addRegOptions() {
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), RequestUtil::getValue('registrationId', 0));
		
		$optionIds = RequestUtil::getValueAsArray('regOpts', array()); 
		foreach($optionIds as $optionId) { 
			$priceId = RequestUtil::getValue('regOptPrice_'.$optionId, 0);
			db_reg_RegOptionManager::getInstance()->createOption($registration['id'], $optionId, $priceId);
		}
		
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), RequestUtil::getValue('registrationId', 0));
		$event = $this->strictFindById(db_EventManager::getInstance(), $registration['eventId']);
		$report = $this->strictFindById(db_ReportManager::getInstance(), RequestUtil::getValue('reportId', 0));	
		
		return new fragment_editRegistrations_regOption_List($event, $report, $registration);
	}
	
	public function cancelRegOption() {
		$id = RequestUtil::getValue('id', 0); // the Registration_RegOption id.
		$groupId = RequestUtil::getValue('groupId', 0);
		$reportId = RequestUtil::getValue('reportId', 0);
		
		db_reg_RegOptionManager::getInstance()->cancel($id);
				
		return new template_Redirect("/admin/registration/Registration?a=view&groupId={$groupId}&reportId={$reportId}");
	}
	
	public function saveVariableQuantity() {
		$registrationId = RequestUtil::getValue('registrationId', 0);
		
		$this->saveVariableQuantityOptions($registrationId);
		
		return new fragment_Success();
	}
	
	private function saveVariableQuantityOptions($registrationId) {
		$currentOpts = db_reg_VariableQuantityManager::getInstance()->findByRegistration(array('id' => $registrationId));
		
		foreach($_REQUEST as $key => $value) {
			if(strpos($key, model_ContentType::$VAR_QUANTITY_OPTION.'_') === 0) {
				$optId = str_replace(model_ContentType::$VAR_QUANTITY_OPTION.'_', '', $key);
				$priceId = RequestUtil::getValue('priceId_'.$optId, 0);
				
				$this->saveVariableQuantityOption($currentOpts, $registrationId, $optId, $priceId, $value);
			}
		}
	}
	
	private function saveVariableQuantityOption($currentOpts, $registrationId, $optId, $priceId, $value) {
		// if the option already exists, then update it.
		foreach($currentOpts as $currentOpt) {
			if($currentOpt['variableQuantityId'] == $optId) {
				// update option
				db_reg_VariableQuantityManager::getInstance()->save(array(
					'id' => $currentOpt['id'], 
					'priceId' => $priceId,
					'quantity' => $value
				));
				
				return;
			}
		}

		// it's a new option.
		db_reg_VariableQuantityManager::getInstance()->createOption(array(
			'registrationId' => $registrationId, 
			'variableQuantityId' => $optId, 
			'priceId' => $priceId, 
			'quantity' => $value
		));
	}
}

?>