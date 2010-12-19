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
		// payment is not required if no total due, so no point in validating.
		$totalDue = model_reg_Registration::getTotalCost($this->event);
		if($totalDue > 0) {
			$errors = $this->validate();
			
			if(!empty($errors)) {
				$summary = new fragment_reg_summary_SummaryPage($this->event);
				return new template_reg_BasePage(array(
					'event' => $this->event,
					'title' => 'Review &amp; Confirm',
					'id' => model_reg_RegistrationPage::$SUMMARY_PAGE_ID,
					'page' => $summary,
					'errors' => $errors
				));
			}
		}
		
		model_reg_Session::addCompletedPage(model_reg_RegistrationPage::$SUMMARY_PAGE_ID);
		
		$this->completeRegistration($this->payment);
		
		$a = new action_reg_Confirmation($this->event);
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
		$registrations = model_reg_Registration::getConvertedRegistrationsFromSession($this->event);
		
		$newRegIds = db_reg_RegistrationManager::getInstance()->createRegistrations($registrations, $payment);

		$completedRegs = array();
		foreach($newRegIds as $id) {
			//$completedRegs[] = db_reg_RegistrationManager::getInstance()->find($id);
		}
		
		$this->sendConfirmationEmail($completedRegs);
	}
	
	private function performPayment() {
		$info = model_reg_Session::getPaymentInfo();
		
		switch($info['paymentType']) {
			case model_PaymentType::$CHECK:
				return array(
					'success' => true,
					'checkNumber' => $info['checkNumber'],
					'amount_tendered' => 0.00
				);
			case model_PaymentType::$PO:
				return array(
					'success' => true,
					'purchaseOrderNumber' => $info['purchaseOrderNumber'],
					'amount_tendered' => 0.00
				);;
			case model_PaymentType::$AUTHORIZE_NET:
				$cost = model_reg_Registration::getTotalCost($this->event);
				
				$authorizeNet = new payment_AuthorizeNET($this->event, $info, $cost);
				$result = $authorizeNet->makePayment();
				
				$result['success'] = intval($result[0], 10) === 1; // AIM response code 1 means approved.
				$result['amount_tendered'] = $cost;
				
				return $result;
		}
	}
	
	private function sendConfirmationEmail($registrations) {
		//TODO
	}
}

?>