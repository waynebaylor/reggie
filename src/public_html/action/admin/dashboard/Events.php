<?php

class action_admin_dashboard_Events extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_dashboard_Events();
		$this->converter = new viewConverter_admin_dashboard_Events();
	}
	
	public function checkRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
	   		model_Role::$SYSTEM_ADMIN, 
	   		model_Role::$EVENT_ADMIN,
	   		// the event doesn't matter, they just need to have a role.
	   		model_Role::$EVENT_MANAGER,		
	   		model_Role::$EVENT_REGISTRAR,	
	   		model_Role::$VIEW_EVENT			
   		));
		
		if(!$hasRole) {
			throw new Exception('User does not have required role.');	
		}
	}
	
	public function view() {
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$info = $this->logic->view(array('user' => $user));
		return $this->converter->getView($info);
	}
	
	public function listEvents() {
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$info = $this->logic->listEvents(array('user' => $user));
		return $this->converter->getListEvents($info);
	}
}

?>
