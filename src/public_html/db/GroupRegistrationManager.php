<?php

class db_GroupRegistrationManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_GroupRegistrationManager();
		}
		
		return self::$instance;
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);

		$obj['fields'] = db_GroupRegistrationFieldManager::getInstance()->findByGroupRegistration($obj);
		
		return $obj;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				eventId,
				enabled,
				defaultRegType
			FROM
				GroupRegistration
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find event group registration.');
	}
	
	public function findByEvent($event) {
		return $this->findByEventId($event['id']);
	}
	
	public function findByEventId($eventId) {
		$sql = '
			SELECT
				id,
				eventId,
				enabled,
				defaultRegType
			FROM
				GroupRegistration
			WHERE
				eventId = :eventId
		';
		
		$params = array(
			'eventId' => $eventId
		);
		
		return $this->queryUnique($sql, $params, 'Find event group registration by event.');
	}
	
	public function createGroupRegistration($eventId) {
		$sql = '
			INSERT INTO
				GroupRegistration(
					eventId,
					enabled,
					defaultRegType
				)
			VALUES(
				:eventId,
				:enabled,
				:defaultRegType
			)
		';
		
		$params = array(
			'eventId' => $eventId,
			'enabled' => 'F',
			'defaultRegType' => 'T'
		);
		
		$this->execute($sql, $params, 'Create event group registration.');
	}
	
	public function save($groupReg) {
		$sql = '
			UPDATE
				GroupRegistration
			SET
				enabled = :enabled,
				defaultRegType = :defaultRegType
			WHERE
				id = :id
		';
		
		$params = ArrayUtil::keyIntersect($groupReg, array('id', 'enabled', 'defaultRegType'));
		
		$this->execute($sql, $params, 'Save event group registration.');
	}
	
	public function deleteByEventId($eventId) {
		$gr = $this->findByEvent(array('id' => $eventId));
		
		foreach($gr['fields'] as $field) {
			db_GroupRegistrationFieldManager::getInstance()->deleteField($field);
		}
		
		$sql = '
			DELETE FROM
				GroupRegistration
			WHERE
				eventId = :eventId
		';
		
		$params = array(
			'eventId' => $eventId
		);
		
		$this->execute($sql, $params, 'Delete group registration for event.');
	}
}

?>