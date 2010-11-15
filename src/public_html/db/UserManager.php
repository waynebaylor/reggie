<?php

class db_UserManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'User';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_UserManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				email,
				isAdmin
			FROM
				User
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find user.');
	}
	
	public function findAll($id) {
		$sql = '
			SELECT
				id,
				email,
				isAdmin
			FROM
				User
		';
		
		return $this->query($sql, array(), 'Find all users.');
	}
	
	public function findByEvent($event) {
		$sql = '
			SELECT
				User.id,
				User.email,
				User.isAdmin
			FROM
				User
			INNER JOIN
				EventPermission
			ON
				User.id = EventPermission.userId
			WHERE
				EventPermission.eventId = :eventId
		';
		
		$params = array(
			'eventId' => $event['id']
		);
		
		return $this->query($sql, $params, 'Find users by event.');
	}
	
	public function authenticate($user) {
		$sql = '
			SELECT
				id,
				email,
				isAdmin
			FROM
				User
			WHERE
				email = :email
			AND
				password = :hash
		';
		
		$params = array(
			'email' => $user['email'],
			'hash' => $this->hash($user)
		);
		
		return $this->rawQueryUnique($sql, $params, 'Authenticate user.');
	}
	
	public function createUser($user) {
		$sql = '
			INSERT INTO
				User (
					email,
					password,
					isAdmin
				)
			VALUES (
				:email,
				:password,
				:isAdmin
			)
		';
		
		$params = array(
			'email' => $user['email'],
			'password' => $this->hash($user),
			'isAdmin' => $user['isAdmin']
		);
		
		$this->execute($sql, $params, 'Create user.');
	}
	
	public function deleteUser($user) {
		// remove permissions before deleting user.
		$this->removeAllPermissions($user);
		
		// delete the user.
		$sql = '
			DELETE FROM
				User
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $user['id']
		);
		
		$this->execute($sql, $params, 'Delete user.');
	}
	
	public function saveUser($user) {
		$sql = '
			UPDATE 
				User
			SET
				email = :email,
				isAdmin = :isAdmin
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $user['id'],
			'email' => $user['email'],
			'password' => $this->hash($user),
			'isAdmin' => $user['idAdmin']
		);
		
		$this->execute($sql, $params, 'Save user.');
	}
	
	public function findEventPermissions($user) {
		$sql = '
			SELECT
				EventPermission.eventId
			FROM
				EventPermission
			WHERE
				userId = :userId
		';
		
		$params = array(
			'userId' => $user['id'],
		);
		
		return $this->rawQuery($sql, $params, 'Find user event permissions.');
	}
	
	public function setEventPermission($user, $events) {
		$this->removeEventPermission($user, $event);
		
		$sql = '
			INSERT INTO
				EventPermission (
					userId,
					eventId
				)
			VALUES (
				:userId,
				:eventId
			)
		'; 
		
		$params = array(
			'userId' => $user['id'],
			'eventId' => $event['id']
		);
		
		$this->execute($sql, $params, 'Give user event permission.');
	}
	
	private function hash($user) {
		return sha1(sha1($user['email'].$user['password']));
	}
	
	private function removeEventPermission($user, $event) {
		$sql = '
			DELETE FROM
				EventPermission
			WHERE
				userId = :userId
			AND
				eventId = :eventId
		';
		
		$params = array(
			'userId' => $user['id'],
			'eventId' => $event['id']
		);
		
		$this->execute($sql, $params, 'Delete user event permissions.');
	}
	
	private function removeAllPermissions($user) {
		$sql = '
			DELETE FROM
				EventPermission
			WHERE
				userId = :userId
		';
		
		$params = array(
			'userId' => $user['id'],
		);
		
		$this->execute($sql, $params, 'Delete all user event permissions.');
	}
}

?>