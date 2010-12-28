<?php

class action_admin_registration_Registration extends action_ValidatorAction
{
	public function view() {
		$groupId = RequestUtil::getValue('groupId', 0);
		
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), $groupId);
		
		$regs = $group['registrations'];
		$firstReg = current($regs);
		$event = $this->strictFindById(db_EventManager::getInstance(), $firstReg['eventId']);
		
		return new template_admin_EditRegistrations($event, $group);	
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
		foreach($_REQUEST as $key => $value) {
			if(strpos($key, model_ContentType::$VAR_QUANTITY_OPTION.'_') === 0) {
				//
			}
		}
	}
}

?>