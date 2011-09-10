<?php

class action_admin_registration_Registration extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_registration_Registration();
		$this->converter = new viewConverter_admin_registration_Registration();
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'groupId' => 0,
			'reportId' => 0
		));
		
		$reportId = $params['reportId']; // reportId is not required.
		$groupId = $params['groupId'];
		$eventId = $params['eventId'];
		
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), $groupId);
		
		$event = $this->strictFindById(db_EventManager::getInstance(), $eventInfo['id']);

		if(empty($reportId)) {
			$report = reset($event['reports']); 
		}
		else {
			$report = db_ReportManager::getInstance()->find($reportId);
		}
		
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
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'registrationId' => 0,
			'reportId' => 0,
			'registrantNumber' => 0
		));
		
		$info = $this->logic->cancelRegistration($params);
		return $this->converter->getCancelRegistration($info);
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
		
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $registrationId);	
		
		$this->logic->sendConfirmation($registrationId);
		
		return new template_Redirect("/admin/registration/Registration?groupId={$registration['regGroupId']}&reportId={$reportId}");
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
		$categoryId = RequestUtil::getValue('categoryId', 0);
		$regGroupId = RequestUtil::getValue('regGroupId', 0);
		$eventId = RequestUtil::getValue('eventId', 0);
		
		$info = $this->logic->addRegistrantToGroup(array(
			'reportId' => $reportId,
			'eventId' => $eventId,
			'regGroupId' => $regGroupId, 
			'categoryId' => $categoryId
		));
		
		return $this->converter->getAddRegistrantToGroup($info);
	}
	
	public function deleteRegistration() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'registrationId' => 0,
			'reportId' => 0
		));
		
		$info = $this->logic->deleteRegistration($params);
		return $this->converter->getDeleteRegistration($info);
	}
	
	public function paymentSummary() {
		$groupId = RequestUtil::getValue('groupId', 0);
		
		$info = $this->logic->paymentSummary($groupId);

		return $this->converter->getPaymentSummary($info);
	}
}

?>