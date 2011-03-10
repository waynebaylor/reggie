<?php

class validation_admin_User
{
	public static function getConfig() {
		return array(
			validation_Validator::required('email', 'Email is required.'),
			validation_Validator::required('password', 'Password is required.')
		);
	}
	
	public static function validate($values) {
		$errors = validation_Validator::validate(self::getConfig(), $values);
		
		// check if the email is already in use.
		if(empty($errors['email'])){
			$user = db_UserManager::getInstance()->findByEmail($values['email']);
			
			if(isset($user) && $user['id'] != ArrayUtil::getValue($values, 'id', 0)) {
				$errors['email'] = 'A user with this email already exists.';
			}
		}
		
		// if they change their email, then they must update their password.
		if(empty($errors['email']) && empty($errors['password'])) {
			$user = db_UserManager::getInstance()->find(ArrayUtil::getValue($values, 'id', 0));
			
			if(!empty($user) && empty($values['password']) && $user['email'] !== $values['email']) {
				$errors['password'] = 'You must also update the password.';
			}
		}
		
		return $errors;
	}
}

?>