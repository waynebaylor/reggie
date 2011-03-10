<?php

class action_admin_event_EditEvent extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_EditEvent();
		$this->converter = new viewConverter_admin_event_EditEvent();
	}
	
	public function view() {
		$id = RequestUtil::getValue('id', 0);
		
		$event = $this->logic->view($id);
		
		return $this->converter->getView(array(
			'event' => $event
		));
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
	
	protected function performSecurityCheck($action) {
		if(in_array($action, array('view', 'saveEvent'))) {
			$user = SessionUtil::getUser();
			security_Restriction::check(array(
				'type' => security_Restriction::$EVENT,
				'user' => $user,
				'eventId' => RequestUtil::getValue('id', 0)
			));
		}
	}
}

?>