<?php

class logic_admin_badge_EditBadgeTemplate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$badgeTemplate = db_BadgeTemplateManager::getInstance()->find($params['id']);
		$eventInfo = db_EventManager::getInstance()->findInfoById($badgeTemplate['eventId']);
		
		return array(
			'template' => $badgeTemplate,
			'eventCode' => $eventInfo['code']
		);
	}
}

?>