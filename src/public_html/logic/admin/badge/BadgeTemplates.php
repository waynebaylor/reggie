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
	
	public function removeTemplate($params) {
		$template = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['id']);
		db_BadgeTemplateManager::getInstance()->delete($template['id']);
		
		return $this->view(array(
			'eventId' => $template['eventId']
		));
	}
}

?>