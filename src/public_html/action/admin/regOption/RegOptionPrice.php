<?php

class action_admin_regOption_RegOptionPrice extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_regOption_RegOptionPrice();
		$this->converter = new viewConverter_admin_regOption_RegOptionPrice();
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
	
	public function addVariableQuantityPrice() {
		$params = RequestUtil::getValues(array(
			'action' => '',
			'eventId' => 0,
			'regOptionId' => 0,
			'description' => '',
			'startDate' => '',
			'endDate' => '',
			'price' => 0.00,
			'regTypeIds' => array(-1)
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_admin_RegOptionPrice::validate($params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->addVariableQuantityPrice($params);
		return $this->converter->getAddVariableQuantityPrice($info);
	}
	
	public function addRegOptionPrice() {
		$params = RequestUtil::getValues(array(
			'action' => '',
			'eventId' => 0,
			'regOptionId' => 0,
			'description' => '',
			'startDate' => '',
			'endDate' => '',
			'price' => 0.00,
			'regTypeIds' => array(-1)
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_admin_RegOptionPrice::validate($params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->addRegOptionPrice($params);
		return $this->converter->getAddRegOptionPrice($info);
	}
	
	public function removePrice() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,	
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removePrice($params);
		return $this->converter->getRemovePrice($info);
	}
	
	public function savePrice() {
		$params = RequestUtil::getValues(array(
			'action' => '',
			'eventId' => 0,
			'id' => 0,
			'description' => '',
			'startDate' => '',
			'endDate' => '',
			'price' => 0.00,
			'regTypeIds' => array(-1)
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_admin_RegOptionPrice::validate($params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->savePrice($params);
		return $this->converter->getSavePrice($info);		
	}
}

?>