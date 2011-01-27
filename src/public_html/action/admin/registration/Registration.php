<?php

class action_admin_registration_Registration extends action_ValidatorAction
{
	private $logic;
	
	function __construct() {
		$this->logic = new logic_admin_registration_Registration();
	}
	
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
		
		$this->saveInformationFields($registrationId, $sectionId);
		
		return new fragment_Success();
	}
	
	public function cancelRegistration() {
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), RequestUtil::getValue('registrationId', 0));
		$reportId = RequestUtil::getValue('reportId', 0);
		
		db_reg_RegistrationManager::getInstance()->cancelRegistration($registration);
		
		return new template_Redirect("/admin/registration/Registration?groupId={$registration['regGroupId']}&reportId={$reportId}");
	}
	
	public function changeRegType() {
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), RequestUtil::getValue('registrationId', 0));
		$regTypeId = RequestUtil::getValue('regTypeId', 0);
		
		// only change if a different reg type is selected.
		if($registration['regTypeId'] != $regTypeId) {
			db_reg_RegistrationManager::getInstance()->changeRegType($registration, $regTypeId);
		}
		
		return new fragment_Success();
	}

	public function sendConfirmation() {
		$registrationId = RequestUtil::getValue('registrationId', 0);
		$reportId = RequestUtil::getValue('reportId', 0);
		
		$this->logic->sendConfirmation($registrationId);
		
		return new template_Redirect("/admin/registration/Registration?groupId={$regGroup['id']}&reportId={$reportId}");
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
	
	public function addRegistrantToGroup() {
		$reportId = RequestUtil::getValue('reportId', 0);
		$regGroupId = RequestUtil::getValue('regGroupId', 0);
		
		$this->logic->addRegistrantToGroup($regGroupId);
		
		return new template_Redirect("/admin/registration/Registration?reportId={$reportId}&groupId={$regGroupId}");
	}
	
	public function createNewRegistration() {
		$eventId = RequestUtil::getValue('eventId', 0);
		$reportId = RequestUtil::getValue('reportId', 0);
		$categoryId = RequestUtil::getValue('categoryId', 0);
		
		$this->logic->createNewRegistration($eventId, $categoryId);
		
		return new fragment_Success();
	}
}

?>