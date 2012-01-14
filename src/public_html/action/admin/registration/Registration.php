<?php

class action_admin_registration_Registration extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_registration_Registration();
		$this->converter = new viewConverter_admin_registration_Registration();
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
	
	public function saveGeneralInfo() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'comments' => ''
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->saveGeneralInfo($params);
		return $this->converter->getSaveGeneralInfo($info);
	}
	
	public function save() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'registrationId' => 0,
			'sectionId' => 0,
			'request' => $_REQUEST
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->save($params);
		return $this->converter->getSave($info);
	}
	
	public function cancelRegistration() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'registrationId' => 0,
			'registrantNumber' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->cancelRegistration($params);
		return $this->converter->getCancelRegistration($info);
	}
	
	public function changeRegType() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'registrationId' => 0,
			'regTypeId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->changeRegType($params);
		return $this->converter->getChangeRegType($info);
	}

	public function sendConfirmation() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'registrationId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->sendConfirmation($params);
		return $this->converter->getSendConfirmation($info);		
	}
	
	public function addRegistrantToGroup() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'regGroupId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->addRegistrantToGroup($params);
		return $this->converter->getAddRegistrantToGroup($info);
	}
	
	public function deleteRegistration() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'registrationId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->deleteRegistration($params);
		return $this->converter->getDeleteRegistration($info);
	}
	
	public function paymentSummary() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'regGroupId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->paymentSummary($params);
		return $this->converter->getPaymentSummary($info);
	}
}

?>