<?php

class action_admin_contactField_ContactField extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_contactField_ContactField();
		$this->converter = new viewConverter_admin_contactField_ContactField();
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
	
	public function addField() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'sectionId' => 0,
			'displayName' => '',
			'code' => '',
			'formInputId' => 0,
			'request' => $_REQUEST
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_ContactField::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
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
	
	public function moveFieldUp() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveFieldUp($params);
		return $this->converter->getMoveFieldUp($info);
	}
	
	public function moveFieldDown() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveFieldDown($params);
		return $this->converter->getMoveFieldDown($info);
	}
	
	public function save() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'displayName' => '',
			'code' => '',
			'formInputId' => 0,
			'regTypeIds' => array(),
			'request' => $_REQUEST		// the logic component uses the request
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_ContactField::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->save($params);
		return $this->converter->getSave($info);
	}
}

?>