<?php

class action_admin_emailTemplate_CreateEmailTemplate extends action_BaseAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_emailTemplate_CreateEmailTemplate();
		$this->converter = new viewConverter_admin_emailTemplate_CreateEmailTemplate();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_event_EditEvent();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function createTemplate() {
		$params = RequestUtil::getValues(array(
			'id' => 0, // used in validation to check for overlap
			'eventId' => 0,
			'enabled' => 'F',
			'contactFieldId' => 0,
			'fromAddress' => '',
			'bcc' => '',
			'regTypeIds' => array(-1),
			'subject' => '',
			'header' => '',
			'footer' => ''
			
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_admin_EmailTemplate::validate($params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->createTemplate($params);
		return $this->converter->getCreateTemplate($info);
	}
}

?>