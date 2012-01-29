<?php

class action_admin_regOption_RegOption extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_regOption_RegOption();
		$this->converter = new viewConverter_admin_regOption_RegOption();
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
	
	public function addOption() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'parentGroupId' => 0,
			'code' => '',
			'description' => '',
			'capacity' => 0,
			'defaultSelected' => 'F',
			'showPrice' => 'F'
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_RegOption::getOptionConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->addOption($params);
		return $this->converter->getAddOption($info);
	}
	
	public function removeOption() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removeOption($params);
		return $this->converter->getRemoveOption($info);
	}
	
	public function moveOptionUp() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveOptionUp($params);
		return $this->converter->getMoveOptionUp($info);
	}
	
	public function moveOptionDown() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveOptionDown($params);
		return $this->converter->getMoveOptionDown($info);
	}
	
	public function saveOption() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'code' => '',
			'description' => '',
			'capacity' => 0,
			'defaultSelected' => 'F',
			'showPrice' => 'F',
			'text' => '',
			'isText' => 'false'
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		if($params['isText'] === 'true') {
			$errors = validation_Validator::validate(validation_admin_RegOption::getTextConfig(), $params);
		}
		else {
			$errors = validation_Validator::validate(validation_admin_RegOption::getOptionConfig(), $params);
		}
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->saveOption($params);
		return $this->converter->getSaveOption($info);
	}
	
	public function addText() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'parentGroupId' => 0,
			'text' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_RegOption::getTextConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->addText($params);
		return $this->converter->getAddText($info);
	}
} 

?>