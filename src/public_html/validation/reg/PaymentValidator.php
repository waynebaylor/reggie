<?php

class validation_reg_PaymentValidator
{
	private $event;
	
	function __construct($event) {
		$this->event = $event;
	}
	
	public function validate() {
		$paymentTypeId = RequestUtil::getValue('paymentType', null);
		
		if(!model_Event::isPaymentTypeEnabled($this->event, array('id' => $paymentTypeId))) {
			throw new Exception("Payment type ID: {$paymentTypeId} not enabled for event ID {$this->event['id']}.");	
		}
		
		switch($paymentTypeId) {
			case model_PaymentType::$CHECK:
				return $this->validateCheck();
			case model_PaymentType::$PO:
				return $this->validatePurchaseOrder();
			case model_PaymentType::$AUTHORIZE_NET:
				return $this->validateAuthorizeNet();
			default:
				throw new Exception('No Payment Type specified.');
		}
	}
	
	private function validateCheck() {
		$errors = array();
		
		$checkNumber = trim(RequestUtil::getValue('checkNumber', ''));
		if(empty($checkNumber)) {
			$errors['checkNumber'] = 'Check Number is required.';
		}
		
		return $errors;
	}
	
	private function validatePurchaseOrder() {
		$errors = array();
		
		$poNumber = trim(RequestUtil::getValue('purchaseOrderNumber', ''));
		if(empty($poNumber)) {
			$errors['purchaseOrderNumber'] = 'PO Number is required.';
		}
		
		return $errors;
	}
	
	private function validateAuthorizeNet() {
		$errors = array();
		
		$cardNumber = trim(RequestUtil::getValue('cardNumber', ''));
		if(empty($cardNumber)) {
			$errors['cardNumber'] = 'Card Number is required.';	
		}
		
		$first = trim(RequestUtil::getValue('firstName', ''));
		if(empty($first)) {
			$errors['firstName'] = 'First Name is required.';
		}
		
		$last = trim(RequestUtil::getValue('lastName', ''));
		if(empty($first)) {
			$errors['lastName'] = 'Last Name is required.';
		}
		
		return $errors;
	}
}
		
?>