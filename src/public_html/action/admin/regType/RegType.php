<?php

class action_admin_regType_RegType extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_regType_RegType();
		$this->converter = new viewConverter_admin_regType_RegType();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_event_EditEvent();
		return $a->hasRole($user, $eventId, $method);	
	}

	public function view() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function saveRegType() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0,
			'description' => '',
			'code' => '',
			'categoryIds' => array()
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_RegType::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->saveRegType($params);
		return $this->converter->getSaveRegType($info);
	}
	
	public function addRegType() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'sectionId' => 0,
			'description' => '',
			'code' => '',
			'categoryIds' => array()
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_RegType::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->addRegType($params);
		return $this->converter->getAddRegType($info);
	}

	public function removeRegType() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removeRegType($params);
		return $this->converter->getRemoveRegType($info);
	}

	public function moveRegTypeUp() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveRegTypeUp($params);
		return $this->converter->getMoveRegTypeUp($info);
	}

	public function moveRegTypeDown() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveRegTypeDown($params);
		return $this->converter->getMoveRegTypeDown($info);
	}
}

?>