<?php

class action_admin_staticPage_CreatePage extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_staticPage_CreatePage();
		$this->converter = new viewConverter_admin_staticPage_CreatePage();
	}
	
	public function view() {
		$info = $this->logic->view(array('eventId' => RequestUtil::getValue('eventId', 0)));
		return $this->converter->getView($info);
	}
	
	public function createPage() {
		$errors = validation_Validator::validate(validation_admin_StaticPage::getConfig(), array(
			'name' => RequestUtil::getValue('name', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->createPage(RequestUtil::getValues(array(
			'eventId' => 0,
			'name' => '',
			'title' => '',
			'content' => ''
		)));
		
		return $this->converter->getCreatePage($info);		
	}
}

?>