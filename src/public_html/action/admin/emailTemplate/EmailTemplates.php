<?php

class action_admin_emailTemplate_EmailTemplates extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_emailTemplate_EmailTemplates();
		$this->converter = new viewConverter_admin_emailTemplate_EmailTemplates();
	}
	
	public static function checkRole($user, $eventId=0, $method='') {
		return action_admin_event_EditEvent::checkRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}	
	
	public function listTemplates() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->listTemplates($params);
		return $this->converter->getListTemplates($info);
	}
	
	public function deleteTemplates() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'emailTemplateIds' => array()
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->deleteTemplates($params);
		return $this->converter->getDeleteTemplates($info);
	}
}

?>