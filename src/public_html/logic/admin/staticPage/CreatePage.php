<?php

class logic_admin_staticPage_CreatePage extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'eventId' => $params['eventId'],
			'page' => array(
				'id' => 0,
				'eventId' => $params['eventId'],
				'name' => '',
				'title' => '',
				'content' => ''
			)
		);
	}
	
	public function createPage($params) {
		db_StaticPageManager::getInstance()->createPage(array(
			'eventId' => $params['eventId'],
			'name' => $params['name'],
			'title' => $params['title'],
			'content' => $params['content']
		));
		
		return array('eventId' => $params['eventId']);
	}
}

?>