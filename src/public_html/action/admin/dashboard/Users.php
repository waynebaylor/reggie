<?php

class action_admin_dashboard_Users extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_dashboard_Users();
		$this->converter = new viewConverter_admin_dashboard_Users();
	}
	
	public function checkRole($user, $eventId, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN, 
			model_Role::$USER_ADMIN
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
	
	public function listUsers() {
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$info = $this->logic->listUsers(array('user' => $user));
		return $this->converter->getListUsers($info);
	}
	
	public function deleteUsers() {
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$info = $this->logic->deleteUsers(array(
			'user' => $user,
			'ids' => RequestUtil::getValueAsArray('ids', array())
		));
		return $this->converter->getDeleteUsers($info);
	}
}

?>