<?php

class logic_admin_user_EditUser extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$createLogic = new logic_admin_user_CreateUser();
		$info = $createLogic->view(array());
		
		$info['user'] = $this->strictFindById(db_UserManager::getInstance(), $params['id']);
		
		return $info;
	}
	
	public function saveUser($params) {
		db_UserManager::getInstance()->saveUser(
			ArrayUtil::keyIntersect($params, array('id', 'email', 'password')));
			
		db_UserManager::getInstance()->removeAllRoles($params['id']);
		
		foreach($params['generalRoles'] as $roleId) {
			db_UserManager::getInstance()->assignUserGeneralRole($params['id'], $roleId);
		}
		
		foreach($params['eventRoles'] as $role) {
			$ids = explode('_', $role);
			$eventId = $ids[0];
			$roleId = $ids[1];
			
			db_UserManager::getInstance()->assignUserEventRole($params['id'], $roleId, $eventId);
		}

		$user = $this->strictFindById(db_UserManager::getInstance(), $params['id']);
		
		return array('user' => $user);
	}
}

?>