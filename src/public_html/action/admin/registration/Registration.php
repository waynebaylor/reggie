<?php

class action_admin_registration_Registration extends action_ValidatorAction
{
	public function view() {
		$reportId = RequestUtil::getValue('reportId', 0);
		$groupId = RequestUtil::getValue('groupId', 0);
		
		$report = $this->strictFindById(db_ReportManager::getInstance(), $reportId);
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), $groupId);
		
		$regs = $group['registrations'];
		$firstReg = reset($regs);
		$event = $this->strictFindById(db_EventManager::getInstance(), $firstReg['eventId']);
		
		return new template_admin_EditRegistrations($event, $report, $group);	
	}
	
	public function saveGeneralInfo() {
		$r = $this->strictFindById(db_reg_RegistrationManager::getInstance(), RequestUtil::getValue('id', 0));
		$r['comments'] = RequestUtil::getValue('comments', '');
		
		db_reg_RegistrationManager::getInstance()->save($r);
		
		return new fragment_Success();
	}
	
	public function save() {
		$registrationId = RequestUtil::getValue('registrationId', 0);
		$sectionId = RequestUtil::getValue('sectionId', 0);
		
		$this->saveRegType($registrationId, $sectionId);
		$this->saveInformationFields($registrationId, $sectionId);
		$this->saveRegOptions($registrationId, $sectionId);
		$this->saveVariableQuantity($registrationId, $sectionId);
		
		return new fragment_Success();
	}
	
	public function saveRegOptions($registrationId, $sectionId) {
		
	}
	
	public function cancelRegOption() {
		$id = RequestUtil::getValue('id', 0); // the Registration_RegOption id.
		$comments = RequestUtil::getValue('comments', '');
		$groupId = RequestUtil::getValue('groupId', 0);
		
		db_reg_RegOptionManager::getInstance()->cancel($id, $comments);
				
		return new template_Redirect('/admin/registration/Registration?a=view&groupId='.$groupId);
	}

	private function saveRegType($registrationId, $sectionId) {
		foreach($_REQUEST as $key => $value) {
			if(strpos($key, model_ContentType::$REG_TYPE.'_') === 0) {
				//
			}
		}
	}
	
	private function saveInformationFields($registrationId, $sectionId) {
		// remove all values in given section. this is necessary because
		// checkboxes/radio buttons may not return a value if not selected.
		db_reg_InformationManager::getInstance()->deleteBySection($registrationId, $sectionId);
		
		// save values.
		foreach($_REQUEST as $key => $value) {
			if(strpos($key, model_ContentType::$CONTACT_FIELD.'_') === 0) {
				$field = array(
					'id' => str_replace(model_ContentType::$CONTACT_FIELD.'_', '', $key),
					'value' => $value
				);
				db_reg_InformationManager::getInstance()->createInformation($registrationId, array($field));
			}
		}
	}
	
	private function saveVariableQuantity($registrationId, $sectionId) {
		$currentOpts = db_reg_VariableQuantityManager::getInstance()->findByRegistration(array('id' => $registrationId));
		
		foreach($_REQUEST as $key => $value) {
			if(strpos($key, model_ContentType::$VAR_QUANTITY_OPTION.'_') === 0) {
				$optId = str_replace(model_ContentType::$VAR_QUANTITY_OPTION.'_', '', $key);
				$priceId = RequestUtil::getValue('priceId_'.$optId, 0);
				$comments = RequestUtil::getValue('comments', '');
				
				$this->saveVariableQuantityOption($currentOpts, $registrationId, $optId, $priceId, $value, $comments);
			}
		}
	}
	
	private function saveVariableQuantityOption($currentOpts, $registrationId, $optId, $priceId, $value, $comments) {
		if(!is_numeric($value) || intval($value, 10) === 0) {
			// delete option
			db_reg_VariableQuantityManager::getInstance()->delete($registrationId, $optId);
			return;
		}

		foreach($currentOpts as $currentOpt) {
			if($currentOpt['variableQuantityId'] == $optId) {
				// update option
				db_reg_VariableQuantityManager::getInstance()->save(array(
					'id' => $currentOpt['id'], 
					'priceId' => $priceId,
					'quantity' => $value,
					'comments' => $comments
				));
				
				return;
			}
		}

		// insert new option
		db_reg_VariableQuantityManager::getInstance()->createOption(array(
			'registrationId' => $registrationId, 
			'variableQuantityId' => $optId, 
			'priceId' => $priceId, 
			'quantity' => $value, 
			'comments' => $comments
		));
	}
	
	public function savePaymentReceived() {
		$groupId = RequestUtil::getValue('regGroupId', 0);
		
		$paymentIds = array();
		
		$payments = ArrayUtil::keyMatches($_REQUEST, '/paymentReceived_(\d+)/');   
		foreach(array_keys($payments) as $p) {
			$match = preg_match('/paymentReceived_(\d+)/', $p, $paymentId);
			if($match === 1) {
				$paymentIds[] = $paymentId[1];
			}
		}
		
		db_reg_PaymentManager::getInstance()->savePaymentReceived($groupId, $paymentIds);
		
		return new fragment_Success();
	}
	
	public function addPayment() {
		error_log(print_r($_REQUEST, true));
		
		return new fragment_Success();
	}
}

?>