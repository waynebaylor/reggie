<?php

class action_admin_registration_Summary extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();

		$this->logic = new logic_admin_registration_Summary();
		$this->converter = new viewConverter_admin_registration_Summary();
	}
	
	public function checkRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));	
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent(
			$user, 
			array(
				model_Role::$EVENT_MANAGER,
				model_Role::$EVENT_REGISTRAR,
				model_Role::$VIEW_EVENT
			), 
			$eventId
		);
		
		if(!$hasRole) {
			throw new Exception('User does not have required role.');
		}
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'regGroupId' => 0,
			'reportId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$params['user'] = $user;
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);		
	}
}

?>