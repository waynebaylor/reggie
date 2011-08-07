<?php

class logic_admin_dashboard_Users extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'user' => $params['user']
		);
	}
	
	public function listUsers($params) {
		return array(
			'user' => $params['user'],
			'users' => db_UserManager::getInstance()->findAll()
		);
	}
	
	public function deleteUsers($params) {
		db_UserManager::getInstance()->deleteUsersById($params['ids']);
		
		return array('user' => $params['user']);
	}
}

?>