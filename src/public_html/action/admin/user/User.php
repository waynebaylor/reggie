<?php

class action_admin_user_User extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$this->checkRole();
		
		$user = $this->strictFindById(db_UserManager::getInstance(), RequestUtil::getValue('id', 0));
		
		return new template_admin_EditUser($user);
	}
	
	public function saveUser() {
		$this->checkRole();
		
		$errors = $this->validate(array('email'));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$user = RequestUtil::getParameters(array('id', 'email', 'password'));
		$user['isAdmin'] = RequestUtil::getValue('isAdmin', 'false');
		
		db_UserManager::getInstance()->saveUser($user);
		
		return new fragment_Success();
	}
	
	public function addUser() {
		$this->checkRole();
		
		$errors = $this->validate(array('email', 'password'));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}

		$user = RequestUtil::getParameters(array('email', 'password'));
		$user['isAdmin'] = RequestUtil::getValue('isAdmin', 'false');
		
		db_UserManager::getInstance()->createUser($user);
		
		return new fragment_user_List();
	}
	
	public function removeUser() {
		$this->checkRole();
		
		$user = $this->strictFindById(db_UserManager::getInstance(), RequestUtil::getValue('id', 0));
		db_UserManager::getInstance()->deleteUser($user);

		return new fragment_user_List();
	}
	
	public function validate($fieldNames = array()) {
		$errors = parent::validate($fieldNames);
		
		// check if the email is already in use.
		if(empty($errors['email'])){
			$user = db_UserManager::getInstance()->findByEmail(RequestUtil::getValue('email', ''));
			if(isset($user) && intval($user['id'], 10) !== intval(RequestUtil::getValue('id', 0))) {
				$errors['email'] = 'A user with this email already exists.';
			}
		}
		
		// if they change their email, then they must update their password.
		if(empty($errors['email']) && empty($errors['password'])) {
			$user = db_UserManager::getInstance()->find(RequestUtil::getValue('id', 0));
			$password = RequestUtil::getValue('password', '');
			if(!empty($user) && empty($password) && $user['email'] !== RequestUtil::getValue('email', '')) {
				$errors['password'] = 'You must also update the password.';
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
	
	private function checkRole() {
		$user = SessionUtil::getAdminUser();
		if(!security_SecurityUtil::isAdmin($user)) {
			throw new Exception("User: {$user['email']} does not have 'Admin' role.");
		}
	}
}

?>