<?php

class action_admin_staticPage_CreatePage extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_staticPage_CreatePage();
		$this->converter = new viewConverter_admin_staticPage_CreatePage();
	}
	
	public function checkRole($user, $eventId, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));	
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent($user, model_Role::$EVENT_MANAGER, $eventId);
		
		if(!$hasRole) {
			throw new Exception('User does not have required role.');
		}
	}
	
	public function view() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function createPage() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'name' => '',
			'title' => '',
			'content' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_StaticPage::getConfig(), array(
			'name' => $params['name']
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->createPage($params);
		return $this->converter->getCreatePage($info);		
	}
}

?>