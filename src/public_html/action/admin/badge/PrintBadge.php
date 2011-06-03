<?php

class action_admin_badge_PrintBadge extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function singleBadge() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0, 
			'registrationId' => 0,
			'badgeTemplateId' => 0
		));
		
		//////////////////////////
		
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		$user = SessionUtil::getUser();
		
		$printTemplate = new printTemplate_MM();
		
		return new fragment_Empty();
	}
}

?>