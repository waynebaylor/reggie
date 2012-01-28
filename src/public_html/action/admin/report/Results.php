<?php

class action_admin_report_Results extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_Results();
		$this->converter = new viewConverter_admin_report_Results();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_report_ReportList();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'reportId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$params['user'] = $user;
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
}

?>