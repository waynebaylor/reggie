<?php

class action_reg_Summary extends action_ValidatorAction
{
	private $event;
	private $payment;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function view() {
		$summary = new fragment_reg_summary_SummaryPage($this->event); 
		return new template_reg_BasePage(array(
			'event' => $this->event,
			'title' => 'Review &amp; Confirm',
			'id' => model_reg_RegistrationPage::$SUMMARY_PAGE_ID,
			'page' => $summary
		));	
	}
	
	/**
	 * alias for Next action. used in the Summary page. 
	 */
	public function Submit() {
		// the summary page's "next" button is labeled "Submit".
		return $this->Next();	
	}
	
	public function Next() {
		// payment is only required if non-zero total due and event has at least one payment type enabled.
		$totalDue = model_reg_Registration::getTotalCost($this->event);
		
		if($totalDue > 0 && !empty($this->event['paymentTypes'])) {
			$errors = $this->validate();
			
			if(!empty($errors)) {
				// if there are payment errors, then we should take the user
				// back to the payment page so they can correct the issue.
				$payment = new fragment_reg_PaymentPage($this->event);
				return new template_reg_BasePage(array(
					'event' => $this->event,
					'title' => 'Payment Information',
					'id' => model_reg_RegistrationPage::$PAYMENT_PAGE_ID,
					'page' => $payment,
					'errors' => $errors
				));
			}
		}
		
		model_reg_Session::addCompletedPage(model_reg_RegistrationPage::$SUMMARY_PAGE_ID);
		
		$regGroup = $this->completeRegistration($this->payment);
		
		$a = new action_reg_Confirmation($this->event, $regGroup);
		return $a->view();
	}

	public function Previous() {
		$a = new action_reg_Payment($this->event);

		// if the event doesn't have any payment types enabled,
		// then skip back to the last reg page.
		if(empty($this->event['paymentTypes'])) {
			return $a->Previous();
		}
		else {
			return $a->view();
		}
	}
	
	public function remove() {
		$index = RequestUtil::getValue('registration', -1);
		
		// don't let user remove ALL registrations.
		if($index >= 0 && count(model_reg_Session::getRegistrations()) > 1) {
			model_reg_Session::removeRegistration($index);
			model_reg_Session::setCurrent(0);
		}

		return $this->view();
	}
	
	public function edit() {
		$index = RequestUtil::getValue('registration', -1);

		if($index >= 0 && $index < count(model_reg_Session::getRegistrations())) {
			model_reg_Session::setCurrent($index);
		}
		
		$category = model_reg_Session::getCategory();
		$cat = model_Category::code($category);
		
		return new template_Redirect("/event/{$this->event['code']}/{$cat}");
	}
	
	public function validate($fieldNames = array()) {
		$errors = parent::validate($fieldNames);
		
		$payment = $this->performPayment();
		
		if($payment['success']) {
			$this->payment = $payment;
		}
		else {
			$message = 'There was a problem processing your payment.';
			
			if(isset($errors['general'])) {
				$errors['general'][] = $message;
			}
			else {
				$errors['general'] = array($message);
			}	
		}
		
		return $errors;
	}
	
	/**
	 * save stuff to the database, send any emails, etc.
	 */
	private function completeRegistration($payment) {
		$registrations = model_reg_Registration::getConvertedRegistrationsFromSession();
		
		$regGroupId = db_reg_RegistrationManager::getInstance()->createRegistrations($registrations, $payment);
		$regGroup = db_reg_GroupManager::getInstance()->find($regGroupId);
		
		if($this->event['emailTemplate']['enabled'] === 'true') {
			$this->sendConfirmationEmail($regGroup);
		}
		
		return $regGroup;
	}
	
	private function performPayment() {
		$info = model_reg_Session::getPaymentInfo();
		
		$cost = model_reg_Registration::getTotalCost($this->event);
		
		switch($info['paymentType']) {
			case model_PaymentType::$CHECK:
				return array(
					'success' => true,
					'paymentType' => model_PaymentType::$CHECK,
					'checkNumber' => $info['checkNumber'],
					'amount' => 0.00
				);
			case model_PaymentType::$PO:
				return array(
					'success' => true,
					'paymentType' => model_PaymentType::$PO,
					'purchaseOrderNumber' => $info['purchaseOrderNumber'],
					'amount' => 0.00
				);;
			case model_PaymentType::$AUTHORIZE_NET:
				$authorizeNet = new payment_AuthorizeNET($this->event, $info, $cost);
				$result = $authorizeNet->makePayment();
				
				$result['paymentType'] = model_PaymentType::$AUTHORIZE_NET;
				$result['name'] = $info['firstName'].' '.$info['lastName'];
				$result['amount'] = $cost;
				$result = array_merge(
					$result, 
					ArrayUtil::keyIntersect($info, array('address', 'city', 'state', 'zip', 'country'))
				);
				
				return $result;
		}
		
		// default is failure.
		return array(
			'success' => false
		);
	}
	
	private function sendConfirmationEmail($regGroup) {
		$logic = new logic_admin_registration_Registration();
		
		foreach($regGroup['registrations'] as $reg) {
			$logic->sendConfirmationEmail($this->event, $regGroup, $reg);
		}
	}
}

?>