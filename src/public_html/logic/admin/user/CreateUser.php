<?php

class logic_admin_user_CreateUser extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'user' => array('id' => 0, 'email' => '', 'password' => '', 'roles' => array()),
			'events' => db_EventManager::getInstance()->findAllInfo(),
			'generalRoles' => db_RoleManager::getInstance()->findGeneralRoles(),
			'eventRoles' => db_RoleManager::getInstance()->findEventRoles()
		);
	}
	
	public function createUser($params) {
		$newUserId = db_UserManager::getInstance()->createUser(array(
			'email' => $params['email'],
			'password' => $params['password']	
		));
		
		foreach($params['generalRoles'] as $roleId) {
			db_UserManager::getInstance()->assignUserGeneralRole($newUserId, $roleId);
		}
		
		foreach($params['eventRoles'] as $role) {
			$ids = explode('_', $role);
			$eventId = $ids[0];
			$roleId = $ids[1];
			
			db_UserManager::getInstance()->assignUserEventRole($newUserId, $roleId, $eventId);
		}
		
		return array(
			'user' => $params['user'],
			'newUserId' => $newUserId
		);
	}
}

?>