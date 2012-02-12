<?php

class logic_admin_staticPage_CreatePage extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $eventInfo['code'],
			'eventId' => $eventInfo['id'],
			'page' => array(
				'id' => 0,
				'eventId' => $eventInfo['id'],
				'name' => '',
				'title' => '',
				'content' => ''
			)
		);
	}
	
	public function createPage($params) {
		db_StaticPageManager::getInstance()->createPage($params);
		
		return array('eventId' => $params['eventId']);
	}
}

?>