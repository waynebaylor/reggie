<?php

class action_admin_staticPage_EditPage extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_staticPage_EditPage();
		$this->converter = new viewConverter_admin_staticPage_EditPage();
	}
	
	public function view() {
		$id = RequestUtil::getValue('id', 0);
		
		$info = $this->logic->view(array(
			'id' => $id
		));
		
		return $this->converter->getView($info);
	}
	
	public function savePage() {
		$errors = validation_Validator::validate(validation_admin_StaticPage::getConfig(), array(
			'name' => RequestUtil::getValue('name', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0,
			'name' => '',
			'title' => '',
			'content' => ''
		));
		
		$info = $this->logic->savePage($params);
		
		return $this->converter->getSavePage($info);
	}
}

?>