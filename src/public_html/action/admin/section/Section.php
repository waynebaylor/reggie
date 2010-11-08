<?php

class action_admin_section_Section extends action_ValidatorAction
{
	private $pageManager;
	private $sectionManager;

	function __construct() {
		parent::__construct();

		$this->pageManager = db_PageManager::getInstance();
		$this->sectionManager = db_PageSectionManager::getInstance();
	}
	
	public function view() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $_REQUEST['eventId']);
		
		return new template_admin_EditSection($event, $section);
	}
	
	public function saveSection() {
		$s = array();
		$s['id'] = RequestUtil::getValue('id', NULL);
		$s['title'] = RequestUtil::getValue('title', '');
		$s['numbered'] = RequestUtil::getValue('numbered', 'false');

		db_PageSectionManager::getInstance()->save($s);
		
		return new fragment_Success();
	}
	
	public function addSection() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment__validation_ValidationErrors($errors);	
		}
		
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['pageId']);

		$title = RequestUtil::getValue('title', '');
		$contentTypeId = $_REQUEST['contentTypeId'];

		$this->sectionManager->createSection($page, $title, $contentTypeId);
	
		$page = db_PageManager::getInstance()->find($page['id']);
		
		return new fragment_section_List($page);
	}

	public function removeSection() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);

		$this->sectionManager->delete($section);

		$page = $this->pageManager->find($section['pageId']);

		return new fragment_section_List($page);
	}

	public function moveSectionUp() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);

		$this->sectionManager->moveSectionUp($section);

		$page = $this->pageManager->find($section['pageId']);

		return new fragment_section_List($page);
	}

	public function moveSectionDown() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);

		$this->sectionManager->moveSectionDown($section);

		$page = $this->pageManager->find($section['pageId']);

		return new fragment_section_List($page);
	}
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'contentTypeId',
				'value' => $_REQUEST['contentTypeId'],
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