<?php

class action_admin_dashboard_ConfirmDeleteEvent extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_dashboard_ConfirmDeleteEvent();
		$this->converter = new viewConverter_admin_dashboard_ConfirmDeleteEvent();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
	   		model_Role::$SYSTEM_ADMIN, 
	   		model_Role::$EVENT_ADMIN	
   		));
   		
   		$hasRole = $hasRole || model_Role::userHasRoleForEvent($user, array(
   			model_Role::$EVENT_MANAGER
   		), $eventId);
   		
   		return $hasRole;
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventIds' => array()
		));
		
		$user = SessionUtil::getUser();
		foreach($params['eventIds'] as $eventId) {
			$this->checkRole($user, $eventId);
		}
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function deleteEvents() {
		$params = RequestUtil::getValues(array(
			'eventIds' => array()
		));
		
		$user = SessionUtil::getUser();
		foreach($params['eventIds'] as $eventId) {
			$this->checkRole($user, $eventId);
		}
		
		$info = $this->logic->deleteEvents($params);
		return $this->converter->getDeleteEvents($info);
	}
}

?>