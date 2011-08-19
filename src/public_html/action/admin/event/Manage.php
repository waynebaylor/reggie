<?php

class action_admin_event_Manage extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_Manage();
		$this->converter = new viewConverter_admin_event_Manage();
	}
	
	private function checkRole($user, $eventId) {
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
		$eventId = RequestUtil::getValue('eventId', 0);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $eventId);
		
		$info = $this->logic->view(array(
			'user' => $user,
			'eventId' => $eventId
		));
		return $this->converter->getView($info);
	}
}

?>