<?php

class action_admin_contactField_Option extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function addOption() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $_REQUEST['contactFieldId']);
		
		if(!empty($_REQUEST['displayName'])) {
			db_ContactFieldOptionManager::getInstance()->createOption(array(
				'contactFieldId' => $field['id'],
				'displayName' => $_REQUEST['displayName'],
				'defaultSelected' => RequestUtil::getValue('defaultSelected', 'false')
			));
		}
		
		$field = db_ContactFieldManager::getInstance()->find($field['id']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_contactFieldOption_List($event, $field);
	}

	public function removeOption() {
		$option = $this->strictFindById(db_ContactFieldOptionManager::getInstance(), $_REQUEST['id']);

		db_ContactFieldOptionManager::getInstance()->delete($option);

		$field = db_ContactFieldManager::getInstance()->find($option['contactFieldId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_contactFieldOption_List($event, $field);
	}
	
	public function moveOptionUp() {
		$option = $this->strictFindById(db_ContactFieldOptionManager::getInstance(), $_REQUEST['id']);
		
		db_ContactFieldOptionManager::getInstance()->moveOptionUp($option);
		
		$field = db_ContactFieldManager::getInstance()->find($option['contactFieldId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_contactFieldOption_List($event, $field);
	}
	
	public function moveOptionDown() {
		$option = $this->strictFindById(db_ContactFieldOptionManager::getInstance(), $_REQUEST['id']);
		
		db_ContactFieldOptionManager::getInstance()->moveOptionDown($option);
		
		$field = db_ContactFieldManager::getInstance()->find($option['contactFieldId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_contactFieldOption_List($event, $field);
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
			)
		);
	}
}

?>