<?php

class action_admin_Login extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$user = SessionUtil::getAdminUser();
		
		if(empty($user)) {
			return new template_admin_Login();			
		}
		else {
			return new template_Redirect('/action/MainMenu?a=view');
		}
	}
	
	public function login() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		return new template_Redirect('/action/MainMenu?a=view');
	}
	
	public function logout() {
		session_destroy();
		return new template_Redirect('/index.php');
	}
	
	public function validate() {
		$errors = parent::validate();

		if(empty($errors)) {
			$info = RequestUtil::getParameters(array('email', 'password'));
			$user = db_UserManager::getInstance()->authenticate($info);

			if(empty($user)) {
				$errors['general'] = array('Invalid email or password.');	
			}
			else {
				SessionUtil::setAdminUser($user);
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