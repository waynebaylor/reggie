<?php

class action_admin_report_CreateReport extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_CreateReport();
		$this->converter = new viewConverter_admin_report_CreateReport();
	}
	
	public function checkRole($user, $eventId, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));	
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent(
			$user, 
			array(
				model_Role::$EVENT_MANAGER,
				model_Role::$EVENT_REGISTRAR
			), 
			$eventId
		);
		
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
	
	public function createReport() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'name' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_Report::getConfig(), array(
			'name' => $params['name']
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->createReport($params);
		return $this->converter->getCreateReport($info);
	}
}

?>