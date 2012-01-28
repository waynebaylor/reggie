<?php

class action_admin_event_CreateEvent extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_CreateEvent();
		$this->converter = new viewConverter_admin_event_CreateEvent();
	}

	public function hasRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN,
			model_Role::$EVENT_MANAGER
		));	
		
		return $hasRole;
	}
	
	public function view() {
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$info = $this->logic->view(array('user' => $user));
		return $this->converter->getView($info);
	}
	
	public function createEvent() {
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$errors = validation_Validator::validate(validation_admin_Event::getConfig(), array(
			'code' => RequestUtil::getValue('code', ''),
			'regOpen' => RequestUtil::getValue('regOpen', ''),
			'regClosed' => RequestUtil::getValue('regClosed', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$eventInfo = RequestUtil::getParameters(array(
			'id',
			'code',
			'displayName',
			'regOpen',
			'regClosed',
			'capacity',
			'confirmationText',
			'regClosedText',
			'cancellationPolicy',
			'paymentInstructions'
		));
		
		$params = array(
			'user' => $user,
			'event' => $eventInfo
		);
		
		$info = $this->logic->createEvent($params);
		return $this->converter->getCreateEvent($info);
	}
}

?>