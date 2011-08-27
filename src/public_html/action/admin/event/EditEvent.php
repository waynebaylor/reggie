<?php

class action_admin_event_EditEvent extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_EditEvent();
		$this->converter = new viewConverter_admin_event_EditEvent();
	}
	
	private function checkRole($user, $eventId) {
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
		
		if(!$hasRole) {
			throw new Exception('User does not have required role.');
		}
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
	
	public function addPage() {
		$errors = validation_Validator::validate(validation_admin_Page::getConfig(), array(
			'title' => RequestUtil::getValue('title', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$eventId = RequestUtil::getValue('eventId', 0);
		$title = RequestUtil::getValue('title', '');
		$categoryIds = RequestUtil::getValueAsArray('categoryIds', array());
		
		$event = $this->logic->addPage($eventId, $title, $categoryIds);
		
		return $this->converter->getAddPage(array(
			'event' => $event
		));
	}
	
	public function removePage() {
		$pageId = RequestUtil::getValue('id', 0);
		
		$event = $this->logic->removePage($pageId);
		
		return $this->converter->getRemovePage(array(
			'event' => $event
		));
	}
	
	public function movePageUp() {
		$pageId = RequestUtil::getValue('id', 0);
		
		$event = $this->logic->movePageUp($pageId);
		
		return $this->converter->getMovePageUp(array(
			'event' => $event
		));
	}
	
	public function movePageDown() {
		$pageId = RequestUtil::getValue('id', 0);
		
		$event = $this->logic->movePageDown($pageId);
		
		return $this->converter->getMovePageDown(array(
			'event' => $event
		));
	}
	
	public function saveEvent() {
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
		
		$this->logic->saveEvent($eventInfo);
		
		return $this->converter->getSaveEvent();
	}
}

?>