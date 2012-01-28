<?php

class action_admin_staticPage_PageList extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_staticPage_PageList();
		$this->converter = new viewConverter_admin_staticPage_PageList();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_staticPage_CreatePage();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function listPages() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->listPages($params);
		return $this->converter->getListPages($info);
	}
	
	public function deletePages() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'pageIds' => array()
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->deletePages($params);
		return $this->converter->getDeletePages($info);
	}
}

?>