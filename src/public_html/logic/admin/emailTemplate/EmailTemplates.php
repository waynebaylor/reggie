<?php

class logic_admin_emailTemplate_EmailTemplates extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'eventId' => $params['eventId']
		);
	}
	
	public function listTemplates($params) {
		$templates = db_EmailTemplateManager::getInstance()->findByEventId($params);
		
		return array(
			'eventId' => $params['eventId'],
			'emailTemplates' => page_admin_emailTemplate_Helper::convert($templates)
		);
	}
	
	public function deleteTemplates($params) {
		db_EmailTemplateManager::getInstance()->deleteTemplates($params);
		
		return array('eventId' => $params['eventId']);
	}
}

?>