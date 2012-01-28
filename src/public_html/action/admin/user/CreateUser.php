<?php

class action_admin_user_CreateUser extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_user_CreateUser();
		$this->converter = new viewConverter_admin_user_CreateUser();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN, 
			model_Role::$USER_ADMIN
		));
		
		return $hasRole;
	}
	
	public function view() {
		$params = array();
		
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$params['user'] = $user;
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function createUser() {
		$params = RequestUtil::getValues(array(
			'email' => '',
			'password' => '',
			'generalRoles' => array(),
			'eventRoles' => array()
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user);

		$params['user'] = $user;
		
		$errors = validation_admin_User::validate($params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->createUser($params);
		return $this->converter->getCreateUser($info);
	}
}

?>