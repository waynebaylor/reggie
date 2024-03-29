<?php

class action_admin_user_EditUser extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_user_EditUser();
		$this->converter = new viewConverter_admin_user_EditUser();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_user_CreateUser();
		return $a->hasRole($user, $eventId, $method);
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
		
		$validateFields = array(
			'id' => RequestUtil::getValue('id', 0),
			'email' => RequestUtil::getValue('email', '')
		);
		$password = RequestUtil::getValue('password', '');
		if(!empty($password)) {
			$validateFields['password'] = $password;
		}
		$errors = validation_admin_User::validate($validateFields);
		
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