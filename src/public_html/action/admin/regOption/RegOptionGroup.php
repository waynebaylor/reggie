<?php

class action_admin_regOption_RegOptionGroup extends action_BaseAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_regOption_RegOptionGroup();
		$this->converter = new viewConverter_admin_regOption_RegOptionGroup();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_event_EditEvent();
		return $a->hasRole($user, $eventId, $method);	
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function addGroup() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'regOptionId' => 0,
			'required' => 'F',
			'multiple' => 'F',
			'minimum' => 0,
			'maximum' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->addGroup($params);
		return $this->converter->getAddGroup($info);
	}
	
	public function removeGroup() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removeGroup($params);
		return $this->converter->getRemoveGroup($info);
	}
	
	public function moveGroupUp() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveGroupUp($params);
		return $this->converter->getMoveGroupUp($info);
	}
	
	public function moveGroupDown() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveGroupDown($params);
		return $this->converter->getMoveGroupDown($info);
	}
	
	public function saveGroup() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'regOptionId' => 0,
			'required' => 'F',
			'multiple' => 'F',
			'minimum' => 0,
			'maximum' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->saveGroup($params);
		return $this->converter->getSaveGroup($info);
	}
}

?>