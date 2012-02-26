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

		$obj['fields'] = db_GroupRegistrationFieldManager::getInstance()->findByGroupRegistration(array(
			'eventId' => $obj['eventId'],
			'groupRegistrationId' => $obj['id']
		));
		
		return $obj;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
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
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find event group registration.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEvent($params) {
		return $this->findByEventId($params);
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEventId($params) {
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
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->queryUnique($sql, $params, 'Find event group registration by event.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function createGroupRegistration($params) {
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
			'eventId' => $params['eventId'],
			'enabled' => 'F',
			'defaultRegType' => 'T'
		);
		
		$this->execute($sql, $params, 'Create event group registration.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, enabled, defaultRegType]
	 */
	public function save($params) {
		$sql = '
			UPDATE
				GroupRegistration
			SET
				enabled = :enabled,
				defaultRegType = :defaultRegType
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('id', 'eventId', 'enabled', 'defaultRegType'));
		
		$this->execute($sql, $params, 'Save event group registration.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$gr = $this->findByEvent($params);
		
		foreach($gr['fields'] as $field) {
			db_GroupRegistrationFieldManager::getInstance()->deleteField(array(
				'eventId' => $params['eventId'],
				'id' => $field['id']
			));
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