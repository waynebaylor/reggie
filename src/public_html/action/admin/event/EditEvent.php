<?php

class action_admin_event_EditEvent extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_EditEvent();
		$this->converter = new viewConverter_admin_event_EditEvent();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));	
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent(
			$user, 
			array(
				model_Role::$EVENT_MANAGER
			), 
			$eventId
		);
		
		return $hasRole;
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'showTab' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function addPage() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'title' => '',
			'categoryIds' => array()
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_Page::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->addPage($params);
		return $this->converter->getAddPage($info);
	}
	
	public function removePage() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removePage($params);
		return $this->converter->getRemovePage($info);
	}
	
	public function movePageUp() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->movePageUp($params);
		return $this->converter->getMovePageUp($info);
	}
	
	public function movePageDown() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->movePageDown($params);
		return $this->converter->getMovePageDown($info);
	}
	
	public function saveEvent() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'code' => '',
			'displayName' => '',
			'regOpen' => '',
			'regClosed' => '',
			'capacity' => 0,
			'confirmationText' => '',
			'regClosedText' => '',
			'cancellationPolicy' => '',
			'paymentInstructions' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_Event::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->saveEvent($params);
		return $this->converter->getSaveEvent($info);
	}
}

?>