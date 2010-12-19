<?php

class action_reg_Payment extends action_ValidatorAction
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function view() {
		$payment = new fragment_reg_PaymentPage($this->event);
		return new template_reg_BasePage(array(
			'event' => $this->event,
			'title' => 'Payment Information',
			'id' => model_reg_RegistrationPage::$PAYMENT_PAGE_ID,
			'page' => $payment
		));
	}
	
	public function Next() {
		// save payment info to the session. this needs to happen before
		// the validation so the user's values are preserved on the error
		// page (if there are errors).
		$this->handlePaymentInformation();
				
		// payment is not required if no total due, so no point in validating.
		$totalDue = model_reg_Registration::getTotalCost($this->event);
		if($totalDue > 0) {
			$errors = $this->validate();
			
			// if there are validation errors, then re-display the page
			// with the necessary error messages.
			if(!empty($errors)) {
				$page = new fragment_reg_PaymentPage($this->event);
				return new template_reg_BasePage(array(
					'event' => $this->event,
					'title' => 'Payment Information',
					'id' => model_reg_RegistrationPage::$PAYMENT_PAGE_ID,
					'page' => $page,
					'errors' => $errors
				));
			}
		}
		
		model_reg_Session::addCompletedPage(model_reg_RegistrationPage::$PAYMENT_PAGE_ID);
		
		model_reg_Registration::removeIncompleteRegistrationsFromSession($this->event);
		
		// go to the next page.
		$a = new action_reg_Summary($this->event);
		return $a->view();
	}

	public function Previous() {
		$category = model_reg_Session::getCategory();
		$pages = model_EventPage::getVisiblePages($this->event, $category);

		$cat = model_Category::code($category);
		$pageId = $pages[count($pages)-1]['id'];
		return new template_Redirect("/event/{$this->event['code']}/{$cat}/{$pageId}");
	}
	
	public function addPerson() {
		model_reg_Session::addPerson($this->event);
		
		$category = model_reg_Session::getCategory();
		$cat = model_Category::code($category);
		
		return new template_Redirect("/event/{$this->event['code']}/{$cat}");
	}
	
	public function validate($fieldNames = array()) {
		$errors = parent::validate($fieldNames);
		
		$validator = new validation_reg_PaymentValidator($this->event);
		
		$errors = array_merge($errors, $validator->validate());
		
		return $errors;
	}
	
	private function handlePaymentInformation() {
		$payment = array(
			'paymentType' => RequestUtil::getValue('paymentType', 0)
		);
		
		switch($payment['paymentType']) {
			case model_PaymentType::$CHECK:
				$payment['checkNumber'] = RequestUtil::getValue('checkNumber', '');
				break;
			case model_PaymentType::$PO:
				$payment['purchaseOrderNumber'] = RequestUtil::getValue('purchaseOrderNumber', '');
				break;
			case model_PaymentType::$AUTHORIZE_NET:
				$payment = array_merge($payment, RequestUtil::getParameters(array(
					'cardNumber',
					'month',
					'year',
					'firstName',
					'lastName',
					'address',
					'city',
					'state',
					'zip',
					'country'
				)));
				break;
		}
		
		model_reg_Session::setPaymentInfo($payment);
	}
}

?>