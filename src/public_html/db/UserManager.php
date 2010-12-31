<?php

class db_UserManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
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
	
	public function findByEmail($email) {
		$sql = '
			SELECT
				id,
				email,
				isAdmin
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
				User_Event
			ON
				User.id = User_Event.userId
			WHERE
				User_Event.eventId = :eventId
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
		$this->removeAllEvents($user);
		
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
		// this is only if they change their password.
		$password = empty($user['password'])? '' : 'password = :password,';
		
		$sql = "
			UPDATE 
				User
			SET
				email = :email,
				{$password}
				isAdmin = :isAdmin
			WHERE
				id = :id
		";
		
		$params = array(
			'id' => $user['id'],
			'email' => $user['email'],
			'isAdmin' => $user['isAdmin']
		);
		
		if(!empty($user['password'])) {
			$params['password'] = $this->hash($user);
		}
		
		$this->execute($sql, $params, 'Save user.');
	}
	
	public function findEventIds($user) {
		$sql = '
			SELECT
				User_Event.eventId
			FROM
				User_Event
			WHERE
				userId = :userId
		';
		
		$params = array(
			'userId' => $user['id'],
		);
		
		$results = $this->rawQuery($sql, $params, 'Find user event ids.');
		
		$eventIds = array();
		foreach($results as $result) {
			$eventIds[] = $result['eventId'];
		}
		
		return $eventIds;
	}
	
	public function setEvent($user, $event) {
		$sql = '
			INSERT INTO
				User_Event (
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
		
		$this->execute($sql, $params, 'Give user access to event.');
	}
	
	private function hash($user) {
		return sha1(sha1($user['email'].$user['password']));
	}
	
	private function removeEvent($user, $event) {
		$sql = '
			DELETE FROM
				User_Event
			WHERE
				userId = :userId
			AND
				eventId = :eventId
		';
		
		$params = array(
			'userId' => $user['id'],
			'eventId' => $event['id']
		);
		
		$this->execute($sql, $params, 'Delete user event access.');
	}
	
	private function removeAllEvents($user) {
		$sql = '
			DELETE FROM
				User_Event
			WHERE
				userId = :userId
		';
		
		$params = array(
			'userId' => $user['id'],
		);
		
		$this->execute($sql, $params, 'Delete all user event access.');
	}
}

?>