<?php

class action_admin_report_GenerateReport extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_GenerateReport();
		$this->converter = new viewConverter_admin_report_GenerateReport();
	}
	
	private function checkRole($user, $eventId) {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));	
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent(
			$user, 
			array(
				model_Role::$EVENT_MANAGER,
				model_Role::$EVENT_REGISTRAR,
				model_Role::$VIEW_EVENT
			), 
			$eventId
		);
		
		if(!$hasRole) {
			throw new Exception('User does not have required role.');
		}
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'reportId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function csv() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'reportId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->csv($params);
		return $this->converter->getCsv($info);
	}
	
	public function search() {
		$params = RequestUtil::getValues(array(
			'reportId' => 0,
			'eventId' => 0,
			'term' => '',
			'contactFieldId' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->search($params);
		return $this->converter->getSearch($info);
	}
}

?>