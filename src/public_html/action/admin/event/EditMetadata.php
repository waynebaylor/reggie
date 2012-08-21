<?php

class action_admin_event_EditMetadata extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_EditMetadata();
		$this->converter = new viewConverter_admin_event_EditMetadata();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_event_EditEvent();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array('eventId' => 0));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function saveMetadata() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'firstName' => 0,
			'lastName' => 0,
			'email' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->saveMetadata($params);
		return $this->converter->getSaveMetadata($info);
	}
}