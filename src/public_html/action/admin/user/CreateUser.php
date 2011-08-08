<?php

class action_admin_user_CreateUser extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_user_CreateUser();
		$this->converter = new viewConverter_admin_user_CreateUser();
	}
	
	private function checkRole($user) {
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
	
	public function createUser() {
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$errors = validation_admin_User::validate(array(
			'email' => RequestUtil::getValue('email', ''), 
			'password' => RequestUtil::getValue('password', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$params = RequestUtil::getParameters(array(
			'email',
			'password',
			'generalRoles',
			'eventRoles'
		));
		
		$params['user'] = $user;
		
		$info = $this->logic->createUser($params);
		return $this->converter->getCreateUser($info);
	}
}

?>