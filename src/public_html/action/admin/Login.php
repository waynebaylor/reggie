<?php

class action_admin_Login extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_Login();
		$this->converter = new viewConverter_admin_Login();
	}
	
	public function view() {
		$user = SessionUtil::getUser();
		
		if(empty($user)) {
			$info = $this->logic->view(array());
			return $this->converter->getView($info);			
		}
		else {
			if(model_Role::userHasRole($user, array(
				model_Role::$SYSTEM_ADMIN,
				model_Role::$EVENT_ADMIN,
				model_Role::$EVENT_MANAGER,
				model_Role::$EVENT_REGISTRAR,
				model_Role::$VIEW_EVENT
			))) {
				$url = '/admin/dashboard/Events';
			}
			else if(model_Role::userHasRole($user, array(model_Role::$USER_ADMIN))) {
				$url = '/admin/dashboard/Users';
			}
			else {
				// no roles, so can't do anything.
				session_destroy();
				$url = '/admin/Login';
			}
			
			return new template_Redirect($url);
		}
	}
	
	public function login() {
		$params = RequestUtil::getValues(array(
			'email' => '',
			'password' => ''
		));
		
		$errors = validation_Validator::validate($this->getValidationConfig(), $params);
		
		if(empty($errors)) {
			$user = db_UserManager::getInstance()->authenticate($params);

			if(empty($user)) {
				$errors['general'] = array('Invalid email or password.');	
			}
			else {
				SessionUtil::setUser($user);
			}
		}
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		return new fragment_Success();
	}
	
	public function logout() {
		session_destroy();
		return new template_Redirect('/admin/Login');
	}
	
	private function getValidationConfig() {
		return array(
			validation_Validator::required('email', 'Email is required.'),
			validation_Validator::required('password', 'Password is required.')
		);
	}
}

?>