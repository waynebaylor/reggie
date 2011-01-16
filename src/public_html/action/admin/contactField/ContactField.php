<?php

class action_admin_contactField_ContactField extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}

	public function view() {
		$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $_REQUEST['id']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $field['eventId']);
		
		return new template_admin_EditContactField($event, $field);
	}
	
	public function addField() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['sectionId']);

		$field = array();
		ObjectUtils::populate($field, $_REQUEST); 
		$field['eventId'] = $section['eventId'];
		
		db_ContactFieldManager::getInstance()->createContactField($field);
		

		$event = db_EventManager::getInstance()->find($section['eventId']);
		$section = db_PageSectionManager::getInstance()->find($section['id']);
		
		return new fragment_contactField_List($event, $section);
	}
	
	public function removeField() {
		$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $_REQUEST['id']);

		$sectionId = $field['sectionId'];
		
		db_ContactFieldManager::getInstance()->delete($field);
		
		$event = db_EventManager::getInstance()->find($field['eventId']);
		$section = db_PageSectionManager::getInstance()->find($sectionId);
		
		return new fragment_contactField_List($event, $section);
	}
	
	public function moveFieldUp() {
		$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $_REQUEST['id']);
		
		db_ContactFieldManager::getInstance()->moveFieldUp($field);
		
		$event = db_EventManager::getInstance()->find($field['eventId']);
		$section = db_PageSectionManager::getInstance()->find($field['sectionId']);
		
		return new fragment_contactField_List($event, $section);
	}
	
	public function moveFieldDown() {
		$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $_REQUEST['id']);
		
		db_ContactFieldManager::getInstance()->moveFieldDown($field);
		
		$event = db_EventManager::getInstance()->find($field['eventId']);
		$section = db_PageSectionManager::getInstance()->find($field['sectionId']);
		
		return new fragment_contactField_List($event, $section);
	}
	
	public function save() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$field = array();
		ObjectUtils::populate($field, $_REQUEST);

		db_ContactFieldManager::getInstance()->save($field);
	
		return new fragment_Success();
	}
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'displayName',
				'value' => $_REQUEST['displayName'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Label is required.'
					)
				)
			),
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
						'regex' => '/^[A-Za-z0-9]+$/',
						'text' => 'Code can only contain letters and numbers.'
					)
				)
			),
			array(
				'name' => 'formInputId',
				'value' => $_REQUEST['formInputId'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Type is required.'
					)
				)
			)
		);
	}
}

?>