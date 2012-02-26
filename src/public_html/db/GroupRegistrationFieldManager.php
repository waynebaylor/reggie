<?php

class db_GroupRegistrationFieldManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_GroupRegistrationFieldManager();
		}
		
		return self::$instance;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
		$sql = '
			SELECT
				GroupRegistration_ContactField.id,
				GroupRegistration_ContactField.groupRegistrationId,
				GroupRegistration_ContactField.contactFieldId,
				ContactField.displayName
			FROM
				GroupRegistration_ContactField
			INNER JOIN
				ContactField
			ON
				GroupRegistration_ContactField.contactFieldId = ContactField.id
			WHERE
				GroupRegistration_ContactField.id = :id
			AND
				ContactField.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find group reg field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, groupRegistrationId]
	 */
	public function findByGroupRegistration($params) {
		$sql = '
			SELECT
				GroupRegistration_ContactField.id,
				GroupRegistration_ContactField.groupRegistrationId,
				GroupRegistration_ContactField.contactFieldId,
				ContactField.displayName
			FROM
				GroupRegistration_ContactField
			INNER JOIN
				ContactField
			ON
				GroupRegistration_ContactField.contactFieldId = ContactField.id
			WHERE
				GroupRegistration_ContactField.groupRegistrationId = :groupRegistrationId 
			AND
				ContactField.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'groupRegistrationId'));
		
		return $this->query($sql, $params, 'Find group reg fields.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, groupRegistrationId, contactFieldId]
	 */
	public function createField($params) {
		$this->checkGroupRegistrationPermission($params);
		
		$sql = '
			INSERT INTO
				GroupRegistration_ContactField(
					groupRegistrationId,
					contactFieldId
				)
			VALUES(
				:groupRegistrationId,
				:contactFieldId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'groupRegistrationId', 
			'contactFieldId')
		);
		
		$this->execute($sql, $params, 'Create group reg field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function deleteField($field) {
		$sql = '
			DELETE FROM
				GroupRegistration_ContactField
			WHERE
				GroupRegistration_ContactField.id = :id
			AND
				GroupRegistration_ContactField.groupRegistrationId
			IN (
				SELECT GroupRegistration.id
				FROM GroupRegistration
				WHERE GroupRegistration.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($field, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete group reg field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, groupRegistrationId]
	 */
	private function checkGroupRegistrationPermission($params) {
		$sql = '
			SELECT
				id,
				eventId
			FROM
				GroupRegistration
			WHERE
				id = :groupRegistrationId
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'groupRegistrationId'));
		
		$results = $this->rawQuery($sql, $params, 'Check group registration permission.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to modify GroupRegistration. (event id, group reg id) -> ({$params['eventId']}, {$params['groupRegistrationId']}).");
		}
	}
}

?>