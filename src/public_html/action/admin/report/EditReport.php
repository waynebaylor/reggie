<?php

class action_admin_report_EditReport extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_EditReport();
		$this->converter = new viewConverter_admin_report_EditReport();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_report_CreateReport();
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
	
	public function saveReport() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'name' => '',
			'type' => model_Report::$STANDARD
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_Report::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->saveReport($params);
		return $this->converter->getSaveReport($info);
	}
	
	public function addField() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'reportId' => 0, 
			'contactFieldId' => '' // can be a string or a number.
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->addField($params);
		return $this->converter->getAddField($info);
	}
	
	public function removeField() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => '', // can be a string or a number.
			'reportId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removeField($params);
		return $this->converter->getRemoveField($info);
	}
	
	public function moveFieldUp() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveFieldUp($params);
		return $this->converter->getMoveFieldUp($info);
	}
	
	public function moveFieldDown() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveFieldDown($params);
		return $this->converter->getMoveFieldDown($info);
	}
}

?>