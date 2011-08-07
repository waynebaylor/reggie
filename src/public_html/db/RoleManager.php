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
			array('id', 'name', 'scope', 'description'), 
			array()
		);
	}
	
	public function findGeneralRoles() {
		return $this->select(
			'Role', 
			array('id', 'name', 'scope', 'description'), 
			array('scope' => 'GENERAL')
		);
	}
	
	public function findEventRoles() {
		return $this->select(
			'Role', 
			array('id', 'name', 'scope', 'description'), 
			array('scope' => 'EVENT')
		);
	}
	
	public function findRolesByUserId($id) {
		$sql = '
			SELECT 
				Role.id,
				Role.name,
				Role.scope,
				Role.description,
				Event.id as eventId,
				Event.code as eventCode,
				Event.displayName as eventDisplayName
			FROM
				Role
			INNER JOIN
				User_Role
			ON 
				Role.id = User_Role.roleId
			LEFT JOIN
				Event
			ON
				User_Role.eventId = Event.id
			WHERE
				User_Role.userId = :userId
		';
		
		$params = array('userId' => $id);
		
		return $this->rawQuery($sql, $params, 'Find user roles.');
	}
}

?>