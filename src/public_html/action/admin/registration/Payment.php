<?php

class action_admin_registration_Payment extends action_ValidatorAction
{
	public function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_registration_Payment();
		$this->converter = new viewConverter_admin_registration_Payment();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_registration_Registration();
		return $a->hasRole($user, $eventId, $method);
	}
		
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function savePayment() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'paymentType' => 0,
			'amount' => 0.00,
			'checkNumber' =>'',
			'purchaseOrderNumber' => '',
			'paymentReceived' => 'F'
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_admin_Payment::validate($params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->savePayment($params);
		return $this->converter->getSavePayment($info);		
	}
	
	public function addPayment() { 
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'regGroupId' => 0,
			'paymentType' => 0,
			'amount' => 0.00,
			'paymentReceived' => 'F',
			'checkNumber' => '',
			'purchaseOrderNumber' => '',
			'cardNumber' => '',
			'month' => '', 
			'year' => '',
			'firstName' => '',
			'lastName' => '',
			'address' => '',
			'city' => '',
			'state' => '',
			'zip' => '',
			'country' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_admin_Payment::validate($params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->addPayment($params);
		
		// processing payment may fail. if so, then display message as validation error.
		if(!$info['success']) {
			return new fragment_validation_ValidationErrors($info['errors']);
		}
		
		return $this->converter->getAddPayment($info);		
	}
	
	public function removePayment() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removePayment($params);
		return $this->converter->getRemovePayment($info);
	}
}

?>