<?php

class action_admin_emailTemplate_CreateEmailTemplate extends action_BaseAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_emailTemplate_CreateEmailTemplate();
		$this->converter = new viewConverter_admin_emailTemplate_CreateEmailTemplate();
	}
	
	public static function checkRole($user, $eventId=0, $method='') {
		return action_admin_event_EditEvent::checkRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));
		$params['emailTemplate'] = array(
			'id' => 0,
			'eventId' => $params['eventId'],
			'enabled' => 'T',
			'contactFieldId' => 0,
			'fromAddress' => '',
			'bcc' => '',
			'regTypeIds' => array(),
			'subject' => '',
			'header' => '',
			'footer' => '' 
		);
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function createTemplate() {
		$params = RequestUtil::getValues(array(
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
		self::checkRole($user, $params['eventId']);
		
		$errors = validation_admin_EmailTemplate::validate($params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->createTemplate($params);
		return $this->converter->getCreateTemplate($info);
	}
}

?>