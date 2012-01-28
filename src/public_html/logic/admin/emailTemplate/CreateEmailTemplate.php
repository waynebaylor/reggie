<?php

class logic_admin_emailTemplate_CreateEmailTemplate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		$newTemplate = array(
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
		
		return array(
			'actionMenuEventLabel' => $eventInfo['code'],
			'eventId' => $params['eventId'],
			'emailTemplate' => $newTemplate
		);
	}
	
	public function createTemplate($params) {
		db_EmailTemplateManager::getInstance()->createEmailTemplate($params);
		
		return array(
			'eventId' => $params['eventId']
		);
	}
}

?>