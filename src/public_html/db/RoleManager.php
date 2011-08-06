<?php

class db_RoleManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_RoleManager();
		}
		
		return self::$instance;
	}
	
	public function findAll() {
		return $this->select(
			'Role', 
			array('id', 'description'), 
			array()
		);
	}
	
	public function findRolesByUserId($id) {
		$sql = '
			SELECT 
				Role.id,
				Role.description,
				User_Role.eventId
			FROM
				Role
			INNER JOIN
				User_Role
			ON 
				Role.id = User_Role.roleId
			WHERE
				User_Role.userId = :userId
		';
		
		$params = array('userId' => $id);
		
		return $this->rawQuery($sql, $params, 'Find user roles.');
	}
}

?>