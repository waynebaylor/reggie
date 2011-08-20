<?php

class action_admin_staticPage_CreatePage extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		
	}
	
	
	
	
public function addPage() {
		$errors = validation_Validator::validate(validation_admin_StaticPage::getConfig(), array(
			'name' => RequestUtil::getValue('name', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'name' => '',
			'title' => ''
		));
		
		$info = $this->logic->addPage($params);
		
		return $this->converter->getAddPage($info);
	}
	
public function addPage($params) {
		db_StaticPageManager::getInstance()->createPage(array(
			'eventId' => $params['eventId'],
			'name' => $params['name'],
			'title' => $params['title']
		));
		
		return $this->view(array(
			'eventId' => $params['eventId']
		));
	}
	
public function getAddPage($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_staticPage_List'));
	}
}

?>