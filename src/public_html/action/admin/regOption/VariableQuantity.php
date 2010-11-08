<?php

class action_admin_regOption_VariableQuantity extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$option = $this->strictFindById(db_VariableQuantityOptionManager::getInstance(), $_REQUEST['id']);
	
		$eventId = $_REQUEST['eventId'];
		$event = db_EventManager::getInstance()->find($eventId);
		
		return new template_admin_EditVariableQuantity($event, $option);
	}
	
	public function addOption() {
		$errors = $this->validate();

		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}

		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['sectionId']);
		
		$option = RequestUtil::getParameters(array(
			'sectionId',
			'code',
			'description',
			'capacity'
		));
		
		db_VariableQuantityOptionManager::getInstance()->createOption($option);
		
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		$section = db_PageSectionManager::getInstance()->find($section['id']);

		return new fragment_variableQuantityOption_List($event, $section);
	}
	
	public function removeOption() {
		$option = $this->strictFindById(db_VariableQuantityOptionManager::getInstance(), $_REQUEST['id']);
		
		db_VariableQuantityOptionManager::getInstance()->delete($option);
		
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		$section = db_PageSectionManager::getInstance()->find($option['sectionId']);

		return new fragment_variableQuantityOption_List($event, $section);
	}
	
	public function saveOption() {
		$errors = $this->validate();

		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$option = RequestUtil::getParameters(array(
			'id',
			'code',
			'description',
			'capacity'
		));
		
		db_VariableQuantityOptionManager::getInstance()->save($option);
		
		return new fragment_Success();
	}
	
	public function moveOptionUp() {
		$option = $this->strictFindById(db_VariableQuantityOptionManager::getInstance(), $_REQUEST['id']);
		
		db_VariableQuantityOptionManager::getInstance()->moveOptionUp($option);
		
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		$section = db_PageSectionManager::getInstance()->find($option['sectionId']);

		return new fragment_variableQuantityOption_List($event, $section);
	}
	
	public function moveOptionDown() {
		$option = $this->strictFindById(db_VariableQuantityOptionManager::getInstance(), $_REQUEST['id']);
		
		db_VariableQuantityOptionManager::getInstance()->moveOptionDown($option);
		
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		$section = db_PageSectionManager::getInstance()->find($option['sectionId']);

		return new fragment_variableQuantityOption_List($event, $section);
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
						'regex' => '/^[A-Za-z0-9]+$/',
						'text' => 'Code can only contain letters and numbers.'
					)
				)
			),
			array(
				'name' => 'description',
				'value' => $_REQUEST['description'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Description is required.'
					)
				)
			)
		);
	}
}

?>