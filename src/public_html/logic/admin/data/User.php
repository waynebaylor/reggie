<?php

class logic_admin_data_User extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function currentUser($params) {
		$user = $this->strictFindById(db_UserManager::getInstance(), $params['id']);
		
		return array(
			'user' => $user
		);
	}
}

?>