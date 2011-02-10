<?php

class action_admin_section_Section extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $section['eventId']);
		
		return new template_admin_EditSection($event, $section);
	}
	
	public function saveSection() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$s = array();
		$s['id'] = RequestUtil::getValue('id', NULL);
		$s['name'] = RequestUtil::getValue('name', '');
		$s['text'] = RequestUtil::getValue('text', NULL);
		$s['numbered'] = RequestUtil::getValue('numbered', 'F');

		db_PageSectionManager::getInstance()->save($s);
		
		return new fragment_Success();
	}
	
	public function addSection() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['pageId']);

		$name = RequestUtil::getValue('name', '');
		$contentTypeId = $_REQUEST['contentTypeId'];

		db_PageSectionManager::getInstance()->createSection($page['eventId'], $page, $name, $contentTypeId);
	
		$page = db_PageManager::getInstance()->find($page['id']);
		
		return new fragment_section_List($page);
	}

	public function removeSection() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);

		db_PageSectionManager::getInstance()->delete($section);

		$page = db_PageManager::getInstance()->find($section['pageId']);

		return new fragment_section_List($page);
	}

	public function moveSectionUp() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);

		db_PageSectionManager::getInstance()->moveSectionUp($section);

		$page = db_PageManager::getInstance()->find($section['pageId']);

		return new fragment_section_List($page);
	}

	public function moveSectionDown() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);

		db_PageSectionManager::getInstance()->moveSectionDown($section);

		$page = db_PageManager::getInstance()->find($section['pageId']);

		return new fragment_section_List($page);
	}
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'name',
				'value' => RequestUtil::getValue('name', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Name is required.'
					)
				)
			),
			array(
				'name' => 'contentTypeId',
				'value' => RequestUtil::getValue('contentTypeId', 0),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Content is required.'
					)
				)
			)
		);
	}
}

?>