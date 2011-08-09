<?php

class action_admin_user_EditUser extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_user_EditUser();
		$this->converter = new viewConverter_admin_user_EditUser();
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
		
		$userId = RequestUtil::getValue('id' , 0);
		
		$info = $this->logic->view(array(
			'id' => $userId	
		));
		return $this->converter->getView($info);
	}
	
	public function saveUser() {
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$errors = validation_admin_User::validate(array(
			'id' => RequestUtil::getValue('id', 0),
			'email' => RequestUtil::getValue('email', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$params = RequestUtil::getParameters(array(
			'id',
			'email',
			'password'
		));
		$params['generalRoles'] = RequestUtil::getValueAsArray('generalRoles', array());
		$params['eventRoles'] = RequestUtil::getValueAsArray('eventRoles', array());
		
		$info = $this->logic->saveUser($params);
		return $this->converter->getSaveUser($info);
	}
}

?>