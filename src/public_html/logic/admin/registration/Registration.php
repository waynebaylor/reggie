<?php

class logic_admin_registration_Registration extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function addRegistrantToGroup($params) {
		$regTypes = db_RegTypeManager::getInstance()->findByEventId($params['eventId']);
		// default is the first reg type whatever it is.
		$regType = reset($regTypes);
		$regTypeId = $regType['id'];
		
		// if we can find a reg type that is specifically visible to the selected category, 
		// then go with that.
		foreach($regTypes as $regType) {
			if(model_RegType::isVisibleTo($regType, array('id' => $params['categoryId']))) {
				$regTypeId = $regType['id'];
			}
		}
		
		$newReg = array(
			'regGroupId' => $params['regGroupId'],
			'categoryId' => $params['categoryId'],
			'regTypeId' => $regTypeId,
			'eventId' => $params['eventId'],
			'information' => array(),
			'regOptionIds' => array(),
			'variableQuantity' => array()
		);
		
		db_reg_RegistrationManager::getInstance()->createRegistration($params['regGroupId'], $newReg);
		
		return array();
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
	
	public function sendConfirmation($registrationId) {
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $registrationId);	
		$event = $this->strictFindById(db_EventManager::getInstance(), $registration['eventId']);
		$regGroup = db_reg_GroupManager::getInstance()->find($registration['regGroupId']);
		
		$this->sendConfirmationEmail($event, $regGroup, $registration);
	}
	
	public function sendConfirmationEmail($event, $regGroup, $registration) {
		$emailTemplate = db_EmailTemplateManager::getInstance()->findByRegTypeId(
			$registration['eventId'], 
			$registration['regTypeId']
		);
		
		if(!empty($emailTemplate)) {
			$to = model_Registrant::getEmailFieldValue($emailTemplate, $registration);
			
			if(!empty($to)) {
				$summaryText = new fragment_registration_emailConfirmation_Confirmation($event, $regGroup);
				$summaryText = $summaryText->html();
				$summaryText = preg_replace('/\s\s+/', ' ', $summaryText); // strip extra whitespace.
				
				$text = <<<_
					<div style="font-family:sans-serif;">
						{$emailTemplate['header']}
						
						<div>{$summaryText}</div>
						
						{$emailTemplate['footer']}
					</div>
_;
			
				EmailUtil::send(array(
					'to' => $to,
					'from' => $emailTemplate['fromAddress'],
					'bcc' => $emailTemplate['bcc'],
					'subject' => $emailTemplate['subject'],
					'text' => $text
				));
			}
		}
	}
	
	public function deleteRegistration($registrationId, $reportId) {
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $registrationId);
		
		db_reg_RegistrationManager::getInstance()->delete($registration);
		
		return array(
			'regGroupId' => $registration['regGroupId'],
			'reportId' => $reportId
		);
	}
	
	public function paymentSummary($groupId) {
		$cost = db_reg_GroupManager::getInstance()->findTotalCost($groupId);
		$paid = db_reg_GroupManager::getInstance()->findTotalPaid($groupId);
		$remainingBalance = $cost - $paid;
		
		$cost = '$'.number_format($cost, 2);
		$paid = '$'.number_format($paid, 2);
		$remainingBalance = '$'.number_format($remainingBalance, 2);
		
		return array(
			'cost' => $cost,
			'paid' => $paid,
			'remainingBalance' => $remainingBalance
		);
	}
	
	public function cancelRegistration($params) {
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $params['registrationId']);
		
		db_reg_RegistrationManager::getInstance()->cancelRegistration($registration);

		return array(
			'regGroupId' => $registration['regGroupId'],
			'reportId' => $params['reportId'],
			'registrantNumber' => $params['registrantNumber']
		);
	}
}

?>