<?php

class logic_admin_emailTemplate_CreateEmailTemplate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $eventInfo['code'],
			'eventId' => $params['eventId'],
			'emailTemplate' => $params['emailTemplate']
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