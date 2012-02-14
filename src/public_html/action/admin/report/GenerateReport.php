<?php

class action_admin_report_GenerateReport extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_GenerateReport();
		$this->converter = new viewConverter_admin_report_GenerateReport();
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
		
		$params['user'] = $user;
		
		$info = $this->logic->csv($params);
		return $this->converter->getCsv($info);
	}
}

?>