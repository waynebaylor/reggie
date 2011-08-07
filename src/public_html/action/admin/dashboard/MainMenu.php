<?php

class action_admin_dashboard_MainMenu extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_dashboard_MainMenu();
		$this->converter = new viewConverter_admin_dashboard_MainMenu();
	}

	public function view() {
		$info = $this->logic->view(SessionUtil::getUser());
		
		return $this->converter->getView($info);
	}
	
	public function addEvent() {
		$errors = validation_Validator::validate(validation_admin_Event::getConfig(), array(
			'code' => RequestUtil::getValue('code', ''),
			'regOpen' => RequestUtil::getValue('regOpen', ''),
			'regClosed' => RequestUtil::getValue('regClosed', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$eventInfo = RequestUtil::getParameters(array('code', 'regOpen', 'regClosed'));
		$eventInfo['displayName'] = RequestUtil::getValue('displayName', '');
		
		$info = $this->logic->addEvent(SessionUtil::getUser(), $eventInfo);
	
		return $this->converter->getAddEvent($info);
	}
	
	public function addUser() {
		$errors = validation_admin_User::validate(array(
			'email' => RequestUtil::getValue('email', ''), 
			'password' => RequestUtil::getValue('password', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}

		$user = RequestUtil::getParameters(array('email', 'password'));
		$user['isAdmin'] = RequestUtil::getValue('isAdmin', 'F');
		
		$info = $this->logic->addUser(SessionUtil::getUser(), $user);
		
		return $this->converter->getAddUser($info);
	}
	
	public function createRegistration() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'categoryId' => 0
		));
		
		$info = $this->logic->createRegistration($params);
		return $this->converter->getCreateRegistration($info);
	}
}

?>