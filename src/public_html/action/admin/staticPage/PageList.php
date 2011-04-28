<?php

class action_admin_staticPage_PageList extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_staticPage_PageList();
		$this->converter = new viewConverter_admin_staticPage_PageList();
	}
	
	public function view() {
		$eventId = RequestUtil::getValue('eventId', 0);
		
		$info = $this->logic->view(array(
			'eventId' => $eventId
		));
		
		return $this->converter->getView($info);
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
	
	public function removePage() {
		$id = RequestUtil::getValue('id', 0);
		
		$info = $this->logic->removePage(array(
			'id' => $id
		));
		
		return $this->converter->getRemovePage($info);
	}
}

?>