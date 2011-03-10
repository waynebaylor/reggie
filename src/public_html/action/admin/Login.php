<?php

class action_admin_Login extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$user = SessionUtil::getUser();
		
		if(empty($user)) {
			return new template_admin_Login();			
		}
		else {
			return new template_Redirect('/admin/dashboard/MainMenu?a=view');
		}
	}
	
	public function login() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		return new fragment_Success();
	}
	
	public function logout() {
		session_destroy();
		return new template_Redirect('/admin/Login');
	}
	
	public function validate($fieldNames = array()) {
		$errors = parent::validate($fieldNames);

		if(empty($errors)) {
			$info = RequestUtil::getParameters(array('email', 'password'));
			$user = db_UserManager::getInstance()->authenticate($info);

			if(empty($user)) {
				$errors['general'] = array('Invalid email or password.');	
			}
			else {
				SessionUtil::setUser($user);
			}
		}
		
		return $errors;
	}
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'email',
				'value' => RequestUtil::getValue('email', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Email is required.'
					)
				)
			),
			array(
				'name' => 'password',
				'value' => RequestUtil::getValue('password', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Password is required.'
					)
				)
			)
		);
	}
}

?>