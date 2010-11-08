<?php

class action_admin_regType_RegType extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}

	public function view() {
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $_REQUEST['id']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $_REQUEST['eventId']);
		
		return new template_admin_EditRegType($event, $regType);
	}
	
	public function saveRegType() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $_REQUEST['id']);
		
		$regType['description'] = $_REQUEST['description'];
		$regType['code'] = $_REQUEST['code'];
		$categoryIds = $_REQUEST['categoryIds'];

		db_RegTypeManager::getInstance()->save($regType, $categoryIds);
		
		return new fragment_Success();
	}
	
	public function addRegType() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['sectionId']);

		$desc = $_REQUEST['description'];
		$code = $_REQUEST['code'];
		$categoryIds = $_REQUEST['categoryIds'];

		$page = db_PageManager::getInstance()->find($section['pageId']);

		db_RegTypeManager::getInstance()->createRegType($page['eventId'], $section['id'], $desc, $code, $categoryIds);

		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		$section = db_PageSectionManager::getInstance()->find($section['id']);
		
		return new fragment_regType_List($event, $section);
	}

	public function removeRegType() {
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $_REQUEST['id']);

		db_RegTypeManager::getInstance()->delete($regType);

		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		$section = db_PageSectionManager::getInstance()->find($regType['sectionId']);

		return new fragment_regType_List($event, $section);
	}

	public function moveRegTypeUp() {
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $_REQUEST['id']);

		db_RegTypeManager::getInstance()->moveRegTypeUp($regType);

		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		$section = db_PageSectionManager::getInstance()->find($regType['sectionId']);

		return new fragment_regType_List($event, $section);
	}

	public function moveRegTypeDown() {
		$regType = $this->strictFindById(db_RegTypeManager::getInstance(), $_REQUEST['id']);
		
		db_RegTypeManager::getInstance()->moveRegTypeDown($regType);

		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		$section = db_PageSectionManager::getInstance()->find($regType['sectionId']);

		return new fragment_regType_List($event, $section);
	}
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'description',
				'value' => $_REQUEST['description'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Description is required.'
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
			)
		);
	}
}

?>