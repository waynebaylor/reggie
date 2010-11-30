<?php

class db_GroupRegistrationManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'GroupRegistration';
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
			'eventId' => $event['id']
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
			'enabled' => 'false',
			'defaultRegType' => 'true'
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
}

?>