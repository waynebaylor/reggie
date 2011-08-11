<?php

class logic_admin_dashboard_Events extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'user' => $params['user']
		);
	}
	
	public function listEvents($params) {
		return array(
			'user' => $params['user'],
			'events' => db_EventManager::getInstance()->findInfoByUserId($params['user']['id'])
		);
	}
}

?>