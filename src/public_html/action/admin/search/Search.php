<?php

class action_admin_search_Search extends action_ValidatorAction
{
	function __construct() {
		parent::__construct(); 
		
		$this->logic = new logic_admin_search_Search();
		$this->converter = new viewConverter_admin_search_Search();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_report_ReportList();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'searchTerm' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function listResults() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'searchTerm' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$params['user'] = $user;
		
		$info = $this->logic->listResults($params);
		return $this->converter->getListResults($info);
	}
}

?>