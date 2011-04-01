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
		
		$regGroup = db_reg_GroupManager::getInstance()->find($registration['regGroupId']);
		$isGroupEmpty = count($regGroup['registrations']) === 0;
		
		return array(
			'regGroupId' => $registration['regGroupId'],
			'reportId' => $reportId,
			'isGroupEmpty' => $isGroupEmpty
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
}

?>