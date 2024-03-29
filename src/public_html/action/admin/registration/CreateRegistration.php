<?php

class action_admin_registration_CreateRegistration extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_registration_CreateRegistration();
		$this->converter = new viewConverter_admin_registration_CreateRegistration();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER,
			model_Role::$EVENT_REGISTRAR
		), $eventId);
		
		return $hasRole;
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function createRegistration() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'regTypeId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->createRegistration($params);
		return $this->converter->getCreateRegistration($info);
	}
}

?>