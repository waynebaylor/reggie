<?php

class logic_admin_badge_BadgeTemplates extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		return array(
			'eventId' => $eventInfo['id'],
			'eventCode' => $eventInfo['code'],
			'templates' => db_BadgeTemplateManager::getInstance()->findByEventId($eventInfo['id'])
		);
	}
	
	public function addTemplate($params) {
		$values = ArrayUtil::keyIntersect($params, array('eventId', 'name', 'regTypeIds'));
		$values['type'] = $params['badgeTemplateType'];
		
		db_BadgeTemplateManager::getInstance()->createBadgeTemplate($values);
		
		return $this->view(array(
			'eventId' => $params['eventId']
		));
	}
	
	public function removeTemplate($params) {
		$template = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['id']);
		db_BadgeTemplateManager::getInstance()->delete($template['id']);
		
		return $this->view(array(
			'eventId' => $template['eventId']
		));
	}
}

?>