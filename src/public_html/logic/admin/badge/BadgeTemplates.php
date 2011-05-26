<?php

class logic_admin_badge_BadgeTemplates extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'eventId' => $params['eventId'],
			'templates' => db_BadgeTemplateManager::getInstance()->findByEventId($params['eventId'])
		);
	}
	
	public function addTemplate($params) {
		db_BadgeTemplateManager::getInstance()->createBadgeTemplate(
			ArrayUtil::keyIntersect($params, array('eventId', 'name', 'regTypeIds'))
		);
		
		return $this->view(array(
			'eventId' => $params['eventId']
		));
	}
}

?>