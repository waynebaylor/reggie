<?php

class logic_admin_emailTemplate_EditEmailTemplate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		$template = db_EmailTemplateManager::getInstance()->find(array(
			'eventId' => $params['eventId'], 
			'id' => $params['emailTemplateId']
		));

		$regTypeIds = array();
		
		if($template['availableToAll'] === true) {
			$regTypeIds[] = -1;	
		}
		else {
			foreach($template['availableTo'] as $regType) {
				$regTypeIds[] = $regType['id'];
			}
		}
		
		$template['regTypeIds'] = $regTypeIds;
		
		return array(
			'actionMenuEventLabel' => $eventInfo['code'],
			'eventId' => $params['eventId'],
			'emailTemplate' => $template
		);
	}
	
	public function saveEmailTemplate($params) {
		db_EmailTemplateManager::getInstance()->save($params);
		
		return array(
			'eventId' => $params['eventId']
		);
	}
	
	public function sendTestEmail($params) {
		$template = db_EmailTemplateManager::getInstance()->find($params);

		$text = $template['header'].'<div>[Registration Summary]</div>'.$template['footer'];

		EmailUtil::send(array(
			'to' => $params['toAddress'],
			'from' => $template['fromAddress'],
			'bcc' => $template['bcc'],
			'subject' => $template['subject'],
			'text' => $text
		));

		return array(
			'eventId' => $params['eventId']
		);
	}
}

?>