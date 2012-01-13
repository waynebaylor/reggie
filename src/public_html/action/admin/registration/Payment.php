<?php

class action_admin_registration_Payment extends action_ValidatorAction
{
	public function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_registration_Payment();
		$this->converter = new viewConverter_admin_registration_Payment();
	}
	
	public static function checkRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER,
			model_Role::$EVENT_REGISTRAR
		), $eventId);
		
		if(!$hasRole) {
			throw new Exception('User does not have required role.');
		}
	}
		
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
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
		self::checkRole($user, $params['eventId']);
		
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
		self::checkRole($user, $params['eventId']);
		
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
}

?>