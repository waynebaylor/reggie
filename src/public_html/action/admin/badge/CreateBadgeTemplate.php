<?php

class action_admin_badge_CreateBadgeTemplate extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_badge_CreateBadgeTemplate();
		$this->converter = new viewConverter_admin_badge_CreateBadgeTemplate();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_badge_BadgeTemplates();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function createTemplate() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'name' => '',
			'type' => '',
			'regTypeIds' => array(-1)
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_BadgeTemplate::getConfig(), array(
			'name' => $params['name'],
			'type' => $params['type'],
			'regTypeIds' => $params['regTypeIds']
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->createTemplate($params);
		return $this->converter->getCreateTemplate($info);
	}
}

?>