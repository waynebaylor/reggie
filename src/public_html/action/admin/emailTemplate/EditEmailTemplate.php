<?php

class action_admin_emailTemplate_EditEmailTemplate extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_emailTemplate_EditEmailTemplate();
		$this->converter = new viewConverter_admin_emailTemplate_EditEmailTemplate();
	}
	
	public static function checkRole($user, $eventId=0, $method='') {
		return action_admin_event_EditEvent::checkRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'emailTemplateId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);		
	}
	
	public function saveEmailTemplate() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
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
		
		$info = $this->logic->saveEmailTemplate($params);
		return $this->converter->getSaveEmailTemplate($info);
	}
	
	public function sendTestEmail() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0,
			'toAddress' => ''
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		if(empty($params['toAddress'])) {
			return new fragment_validation_ValidationErrors(array('toAddress' => 'To Address is required.'));	
		}
		
		$info = $this->logic->sendTestEmail($params);
		return $this->converter->getSendTestEmail($info);
	}
}

?>