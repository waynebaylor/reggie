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