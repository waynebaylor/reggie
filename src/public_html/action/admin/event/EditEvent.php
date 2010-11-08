<?php

class action_admin_event_EditEvent extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$id = $_REQUEST['id'];
		$event = db_EventManager::getInstance()->find($id);
		
		if(empty($event)) {
			return new template_ErrorPage();
		}
		else {
			return new template_admin_EditEvent($event);
		}
	}
	
	public function addEvent() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$event = RequestUtil::getParameters(array('code', 'displayName', 'regOpen', 'regClosed'));
		
		$id = db_EventManager::getInstance()->createEvent($event);
		$event = db_EventManager::getInstance()->find($id);
		
		FileUtil::createEventDir($event);

		return new fragment_event_List();
	}
	
	public function saveEvent() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$event = array();
		ObjectUtils::populate($event, $_REQUEST);
		
		$oldEvent = db_EventManager::getInstance()->find($event['id']);
		
		db_EventManager::getInstance()->save($event);
		
		FileUtil::renameEventDir($oldEvent, $event);
		
		return new fragment_Success();	
	}
	
	public function validate() {
		$errors = parent::validate();
		
		// check if an event with this code already exists.
		if(empty($errors['code'])) {
			$event = db_EventManager::getInstance()->findByCode($_REQUEST['code']); 
			if(isset($event) && intval($event['id'], 10) !== intval($_REQUEST['id'], 10)) {
				$errors['code'] = 'An event with this Code already exists.';
			}
		}
		
		return $errors;
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
						'text' => 'Enter date as \'yyyy-MM-dd\' or \'yyyy-MM-dd HH:mm\'.'
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
						'text' => 'Enter date as \'yyyy-MM-dd\' or \'yyyy-MM-dd HH:mm\'.'
					)
				)
			)
		);
	}
}

?>