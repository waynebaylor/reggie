<?php

class db_UserManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
	
		$obj['roles'] = db_RoleManager::getInstance()->findRolesByUserId($obj['id']);
		
		return $obj;
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
				email
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
	
	public function findByEmail($email) {
		$sql = '
			SELECT
				id,
				email
			FROM
				User
			WHERE
				email = :email
		';
		
		$params = array(
			'email' => $email
		);
		
		return $this->queryUnique($sql, $params, 'Find user by email.');
	}
	
	public function findAll() {
		$sql = '
			SELECT
				id,
				email
			FROM
				User
		';
		
		return $this->query($sql, array(), 'Find all users.');
	}
	
	public function authenticate($user) {
		$sql = '
			SELECT
				id,
				email
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
		
		return $this->queryUnique($sql, $params, 'Authenticate user.');
	}
	
	public function createUser($user) {
		$sql = '
			INSERT INTO
				User (
					email,
					password
				)
			VALUES (
				:email,
				:password
			)
		';
		
		$params = array(
			'email' => $user['email'],
			'password' => $this->hash($user)
		);
		
		$this->execute($sql, $params, 'Create user.');
	}
	
	public function deleteUsersById($ids) {
		if(!is_array($ids)) {
			$ids = array($ids);
		}

		// delete all user roles.
		$sql = '
			DELETE FROM
				User_Role
			WHERE
				userId in (:[ids])
		';

		$params = array(
			'ids' => $ids
		);
		
		$this->execute($sql, $params, 'Delete user roles.');
		
		// delete from user table.
		$sql = '
			DELETE FROM
				User
			WHERE
				id in (:[ids])
		';
		
		$this->execute($sql, $params, 'Delete users.');
	}
	
	public function saveUser($user) {
		// this is only if they change their password.
		$password = empty($user['password'])? '' : 'password = :password,';
		
		$sql = "
			UPDATE 
				User
			SET
				email = :email,
				{$password}
			WHERE
				id = :id
		";
		
		$params = array(
			'id' => $user['id'],
			'email' => $user['email']
		);
		
		if(!empty($user['password'])) {
			$params['password'] = $this->hash($user);
		}
		
		$this->execute($sql, $params, 'Save user.');
	}
	
	private function hash($user) {
		return sha1(sha1($user['email'].$user['password']));
	}
}

?>