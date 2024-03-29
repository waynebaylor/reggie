<?php

class action_admin_staticPage_EditPage extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_staticPage_EditPage();
		$this->converter = new viewConverter_admin_staticPage_EditPage();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_staticPage_CreatePage();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'pageId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function savePage() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0,
			'name' => '',
			'title' => '',
			'content' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_StaticPage::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->savePage($params);
		return $this->converter->getSavePage($info);
	}
}

?>