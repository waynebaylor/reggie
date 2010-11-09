<?php

class action_admin_page_Page extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}

	public function view() {
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $page['eventId']);
		
		return new template_admin_EditPage($event, $page);
	}
	
	public function savePage() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);
		
		$page['title'] = $_REQUEST['title'];
		$categoryIds = $_REQUEST['categoryIds'];
		
		db_PageManager::getInstance()->savePage($page, $categoryIds);
		
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

		db_PageManager::getInstance()->createPage($event, $title, $categories);

		$event = db_EventManager::getInstance()->find($event['id']);
			
		return new fragment_page_List($event);
	}
	
	public function removePage() {
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);

		db_PageManager::getInstance()->deletePage($page);
		$event = db_EventManager::getInstance()->find($page['eventId']);
			
		return new fragment_page_List($event);
	}
	
	public function movePageUp() {
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);

		db_PageManager::getInstance()->movePageUp($page);
		$event = db_EventManager::getInstance()->find($page['eventId']);
			
		return new fragment_page_List($event);
	}
	
	public function movePageDown() {
		$page = $this->strictFindById(db_PageManager::getInstance(), $_REQUEST['id']);
		
		db_PageManager::getInstance()->movePageDown($page);
		$event = db_EventManager::getInstance()->find($page['eventId']);
			
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