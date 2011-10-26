<?php

class action_admin_page_Page extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_page_Page();
		$this->converter = new viewConverter_admin_page_Page();
	}

	public static function checkRole($user, $eventId=0, $method='') {
		return action_admin_event_EditEvent::checkRole($user, $eventId, $method);	
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function savePage() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0,
			'title' => '',
			'categoryIds' => array()
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_Page::getConfig(), array(
			'titke' => $params['title']
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->savePage($params);
		return $this->converter->getSavePage($info);
	}
}
?>