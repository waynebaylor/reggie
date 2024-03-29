<?php

class action_admin_report_ReportList extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_ReportList();
		$this->converter = new viewConverter_admin_report_ReportList();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
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
		
		return $hasRole;
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
	
	public function listReports() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->listReports($params);
		return $this->converter->getListReports($info);
	}
	
	public function deleteReports() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0),
			'reportIds' => RequestUtil::getValueAsArray('reportIds', array())
		);
		
		// permission to delete reports is more restrictive than 
		// listing them, thus we use CreateReport->hasRole() instead
		// of ReportList->hasRole().
		$user = SessionUtil::getUser();
		$a = new action_admin_report_CreateReport();
		$a->checkRole($user, $params['eventId']);
		
		$info = $this->logic->deleteReports($params);
		return $this->converter->getDeleteReports($info);
	}
}

?>