<?php

class action_admin_page_Page extends action_ValidatorAction
{
	private $eventManager;
	private $pageManager;

	function __construct() {
		parent::__construct();

		$this->eventManager = db_EventManager::getInstance();
		$this->pageManager = db_PageManager::getInstance();
	}

	public function view() {
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);
		
		return new template_admin_EditPage($page);
	}
	
	public function savePage() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);
		
		$page['title'] = $_REQUEST['title'];
		$categoryIds = $_REQUEST['categoryIds'];
		
		$this->pageManager->savePage($page, $categoryIds);
		
		return new fragment_Success();
	}
	
	public function addPage() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$event = $this->strictFindById(db_EventManager::getInstance(), $_REQUEST['eventId']);
		
		$title = $_REQUEST['title'];
		$title = empty($title)? 'New Page' : $title;
		$categories = $_REQUEST['categoryIds'];

		$this->pageManager->createPage($event, $title, $categories);

		$event = $this->eventManager->find($event['id']);
			
		return new fragment_page_List($event);
	}
	
	public function removePage() {
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);

		$this->pageManager->deletePage($page);
		$event = $this->eventManager->find($page['eventId']);
			
		return new fragment_page_List($event);
	}
	
	public function movePageUp() {
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);

		$this->pageManager->movePageUp($page);
		$event = $this->eventManager->find($page['eventId']);
			
		return new fragment_page_List($event);
	}
	
	public function movePageDown() {
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);
		
		$this->pageManager->movePageDown($page);
		$event = $this->eventManager->find($page['eventId']);
			
		return new fragment_page_List($event);
	}

	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'title',
				'value' => $_REQUEST['title'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Page Title is required.'
					)
				)
			)
		);
	}
}
?>