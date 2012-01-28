<?php

class action_admin_event_EditGroupRegistration extends action_BaseAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_EditGroupRegistration();
		$this->converter = new viewConverter_admin_event_EditGroupRegistration();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_event_EditEvent();
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
	
	public function saveGroupReg() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0,
			'enabled' => 'F',
			'defaultRegType' => 'F'
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->saveGroupReg($params);
		return $this->converter->getSaveGroupReg($info);
	}
	
	public function addField() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'groupRegistrationId' => 0,
			'contactFieldId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->addField($params);
		return $this->converter->getAddField($info);
	}
	
	public function removeField() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removeField($params);
		return $this->converter->getRemoveField($info);		
	}
}

?>