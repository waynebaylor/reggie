<?php

class logic_admin_user_CreateUser extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'events' => db_EventManager::getInstance()->findAllInfo(),
			'generalRoles' => db_RoleManager::getInstance()->findGeneralRoles(),
			'eventRoles' => db_RoleManager::getInstance()->findEventRoles()
		);
	}
}

?>