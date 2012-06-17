<?php

class logic_admin_registration_Registration extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), $params['id']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);

		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'group' => $group,
			'breadcrumbsParams' => array(
				'altEventId' => $params['eventId'], // don't want 'Event' link showing up
				'regGroupId' => $group['id']
			)
		);
	}
	
	public function saveGeneralInfo($params) {
		$r = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $params['id']);
		$r['comments'] = $params['comments'];
		
		db_reg_RegistrationManager::getInstance()->save($r);
		
		return $params;
	}
	
	public function save($params) {
		// remove all values in given section. this is necessary because
		// checkboxes/radio buttons may not return a value if not selected.
		db_reg_InformationManager::getInstance()->deleteBySection($params);
		
		// save values.
		foreach($params['request'] as $key => $value) {
			if(strpos($key, model_ContentType::$CONTACT_FIELD.'_') === 0) {
				$field = array(
					'id' => str_replace(model_ContentType::$CONTACT_FIELD.'_', '', $key),
					'value' => $value
				);
				db_reg_InformationManager::getInstance()->createInformation($params['registrationId'], array($field));
			}
		}
		
		return $params;
	}
	
	public function changeRegType($params) {
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $params['registrationId']);
		$regTypeId = $params['regTypeId'];
		
		// only change if a different reg type is selected.
		if($registration['regTypeId'] != $regTypeId) {
			db_reg_RegistrationManager::getInstance()->changeRegType($registration, $regTypeId);
		}
		
		return $params;
	}
	
	public function sendConfirmation($params) {
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $params['registrationId']);	
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		$regGroup = db_reg_GroupManager::getInstance()->find($registration['regGroupId']);
		
		$this->sendConfirmationEmail($event, $regGroup, $registration);
		
		return array(
			'eventId' => $params['eventId'],
			'regGroupId' => $regGroup['id']
		);		
	}
	
	public function addRegistrantToGroup($params) {
		$newReg = array(
			'regGroupId' => $params['regGroupId'],
			'eventId' => $params['eventId'],
			'information' => array(),
			'regOptionIds' => array(),
			'variableQuantity' => array()
		);
		
		$groupRegistrations = db_reg_RegistrationManager::getInstance()->findByRegistrationGroupId($params['regGroupId']);
		$sampleRegistration = reset($groupRegistrations);
		
		// if there are no registrants in the group, then use the first
		// reg type for the event. these can be changed after the registration is created.
		if($sampleRegistration === FALSE) {
			$eventRegTypes = db_RegTypeManager::getInstance()->findByEventId($params);
			$defaultRegType = reset($eventRegTypes);
			$newReg['regTypeId'] = $defaultRegType['id'];
			
			$eventCategories = db_CategoryManager::getInstance()->findByRegType($defaultRegType);
			$defaultCategory = reset($eventCategories);
			$newReg['categoryId'] = $defaultCategory['id'];
		}
		else {
			$newReg['regTypeId'] = $sampleRegistration['regTypeId'];
			$newReg['categoryId'] = $sampleRegistration['categoryId'];
		}
		
		$newRegId = db_reg_RegistrationManager::getInstance()->createRegistration($params['regGroupId'], $newReg);
		
		db_reg_RegistrationManager::getInstance()->createLeadNumber($params['eventId'], $newRegId);
		
		$group = db_reg_GroupManager::getInstance()->find($params['regGroupId']);
		$count = count($group['registrations']);
		
		return array(
			'eventId' => $params['eventId'],
			'groupId' => $params['regGroupId'],
			'newNumber' => $count
		);
	}
	
	public function sendConfirmationEmail($event, $regGroup, $registration) {
		$emailTemplate = db_EmailTemplateManager::getInstance()->findByRegTypeId($registration);
		
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
	
	public function deleteRegistration($params) {
		$registrationId = $params['registrationId'];
		
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $registrationId);
		
		db_reg_RegistrationManager::getInstance()->delete($registration);
		
		return array(
			'eventId' => $params['eventId'],
			'regGroupId' => $registration['regGroupId']
		);
	}
	
	public function paymentSummary($params) {
		$groupId = $params['regGroupId'];
		
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
			'eventId' => $params['eventId'],
			'regGroupId' => $registration['regGroupId'],
			'registrantNumber' => $params['registrantNumber']
		);
	}
}

?>