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
	
	public function find($id) {
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
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find group reg field.');
	}
	
	public function findByGroupRegistration($groupReg) {
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
		';
		
		$params = array(
			'groupRegistrationId' => $groupReg['id']
		);
		
		return $this->query($sql, $params, 'Find group reg fields.');
	}
	
	public function createField($field) {
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
		
		$params = ArrayUtil::keyIntersect($field, array(
			'groupRegistrationId', 
			'contactFieldId')
		);
		
		$this->execute($sql, $params, 'Create group reg field.');
	}
	
	public function deleteField($field) {
		$sql = '
			DELETE FROM
				GroupRegistration_ContactField
			WHERE
				id = :id
		';
		
		$params = ArrayUtil::keyIntersect($field, array('id'));
		
		$this->execute($sql, $params, 'Delete group reg field.');
	}
}

?>