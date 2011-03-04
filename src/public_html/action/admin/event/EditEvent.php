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
	
	public function addEvent() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$event = RequestUtil::getParameters(array('code', 'displayName', 'regOpen', 'regClosed'));
		
		$id = db_EventManager::getInstance()->createEvent($event);
		db_UserManager::getInstance()->setEvent(SessionUtil::getUser(), array('id' => $id));
		
		$event = db_EventManager::getInstance()->find($id);
		
		FileUtil::createEventDir($event);

		return new fragment_event_List();
	}
	
	public function saveEvent() {
		$errors = $this->validate();
		
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
	
	public function validate($fieldNames = array()) {
		$errors = parent::validate($fieldNames);
		
		// check if an event with this code already exists.
		if(empty($errors['code'])) {
			$event = db_EventManager::getInstance()->findByCode($_REQUEST['code']); 
			if(isset($event) && intval($event['id'], 10) !== intval(RequestUtil::getValue('id', 0), 10)) {
				$errors['code'] = 'An event with this Code already exists.';
			}
		}
		
		return $errors;
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
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'code',
				'value' => $_REQUEST['code'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Code is required.'
					),
					array(
						'name' => 'pattern',
						'regex' => '/^[A-Za-z]/',
						'text' => 'Code must begin with a letter.'
					),
					array(
						'name' => 'pattern',
						'regex' => '/^[A-Za-z][_A-Za-z0-9]*$/',
						'text' => 'Code can only contain letters, numbers, and underscore.'
					)
				)
			),
			array(
				'name' => 'regOpen',
				'value' => $_REQUEST['regOpen'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Reg Open is required.'
					),
					array(
						'name' => 'pattern',
						'regex' => '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2})?$/',
						'text' => 'Enter date as "yyyy-MM-dd" or "yyyy-MM-dd HH:mm".'
					)
				)
			),
			array(
				'name' => 'regClosed',
				'value' => $_REQUEST['regClosed'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Reg Closed is required.'
					),
					array(
						'name' => 'pattern',
						'regex' => '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2})?$/',
						'text' => 'Enter date as "yyyy-MM-dd" or "yyyy-MM-dd HH:mm".'
					)
				)
			)
		);
	}
}

?>