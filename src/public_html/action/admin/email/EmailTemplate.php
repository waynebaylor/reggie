<?php

class action_admin_email_EmailTemplate extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$eventId = RequestUtil::getValue("id", 0);		
		$event = $this->strictFindById(db_EventManager::getInstance(), $eventId);
		
		return new template_admin_EmailTemplate($event);
	}
	
	public function saveTemplate() {
		$template = RequestUtil::getParameters(array(
			'id', 
			'contactFieldId',
			'enabled',
			'fromAddress',
			'bcc',
			'subject',
			'header',
			'footer'	
		));
		
		db_EmailTemplateManager::getInstance()->save($template);
		
		return new fragment_Success();
	}
	
	public function sendTest() {
		$to = RequestUtil::getValue('to', '');
		
		if(!empty($to)) {
			$template = $this->strictFindById(db_EmailTemplateManager::getInstance(), RequestUtil::getValue('id', 0));

			$text = $template['header'].' [Registration Summary] '.$template['footer'];

			EmailUtil::send(array(
				'to' => $to,
				'from' => $template['fromAddress'],
				'bcc' => $template['bcc'],
				'subject' => $template['subject'],
				'text' => $text
			));
		}
		
		return $this->view();
	}
}

?>