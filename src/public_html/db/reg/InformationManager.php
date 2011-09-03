<?php

class db_reg_InformationManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_reg_InformationManager();
		}
		
		return self::$instance;
	}
	
	public function findByRegistration($r) {
		$sql = '
			SELECT
				id,
				registrationId,
				contactFieldId,
				value
			FROM
				Registration_Information
			WHERE
				registrationId=:id
		';
		
		$params = array(
			'id' => $r['id']
		);
		
		$results = $this->query($sql, $params, 'Find registration information.');
		
		// if a field allows multiple values (checkbox) they will all be put in an array.
		$infos = array();
		foreach($results as $result) {
			$fieldId = $result['contactFieldId'];
			$value = $result['value'];
			
			if(empty($infos[$fieldId])) {
				// first value for this field.
				$infos[$fieldId] = $result;
				$infos[$fieldId]['value'] = $value;
			}
			else {
				// if there are multiple values for a field, then
				// put them in an array.
				$fieldValue = $infos[$fieldId]['value'];
				if(is_array($fieldValue)) {
					$infos[$fieldId]['value'][] = $value;
				}
				else {
					$infos[$fieldId]['value'] = array($fieldValue, $value);
				}
			}
		}

		return $infos;
	}
	
	/**
	 * creates the information rows associated with the given 
	 * reg id.
	 * 
	 * @param $id
	 * @param $infos
	 */
	public function createInformation($id, $infos) {
		foreach($infos as $field) {
			// checkboxes and select values will be arrays. for other input
			// types convert value to array.
			$values = is_array($field['value'])? $field['value'] : array($field['value']);
				
			foreach($values as $v) {
				$sql = '
					INSERT INTO
						Registration_Information(
							registrationId,
							contactFieldId,
							value	
						)
					VALUES(
						:registrationId,
						:contactFieldId,
						:value
					)
				';
					
				$params = array(
					'registrationId' => $id,
					'contactFieldId' => $field['id'],
					'value' => $v
				);
					
				$this->execute($sql, $params, 'Create registration information.');
			}
		}
	}
	
	public function deleteBySection($registrationId, $sectionId) {
		$fields = db_ContactFieldManager::getInstance()->findBySection(array('id' => $sectionId));
		foreach($fields as $field) {
			$sql = '
				DELETE FROM
					Registration_Information
				WHERE
					registrationId = :registrationId
				AND
					contactFieldId = :contactFieldId
			';
			
			$params = array(
				'registrationId' => $registrationId,
				'contactFieldId' => $field['id']
			);
			
			$this->execute($sql, $params, 'Delete registration information by section.');
		}
	}
	
	public function retainFieldsByRegType($registrationId, $regTypeId) {
		$sql = '
			DELETE FROM
				Registration_Information
			WHERE
				Registration_Information.registrationId = :registrationId
			AND
				Registration_Information.contactFieldId NOT IN (
					SELECT 
						RegTypeContactField.contactFieldId
					FROM
						RegTypeContactField
					WHERE
						RegTypeContactField.regTypeId = :regTypeId
					OR
						RegTypeContactField.regTypeId IS NULL
				)
		';
		
		$params = array(
			'registrationId' => $registrationId,
			'regTypeId' => $regTypeId
		);
		
		$this->execute($sql, $params, 'Retain information fields by reg type.');
	}
	
	public function deleteByRegistrationId($registrationId) {
		$sql = '
			DELETE FROM
				Registration_Information
			WHERE
				registrationId = :registrationId
		';
		
		$params = array(
			'registrationId' => $registrationId
		);
		
		$this->execute($sql, $params, 'Delete registration information.');
	}
	
	public function searchInformationValues($params) {
		$sql = "
			(
				SELECT
					Registration.id as registrationId,
					Registration.regGroupId,
					Registration_Information.value as value, 
					ContactField.displayName
				FROM
					Registration_Information
				INNER JOIN
					Registration
				ON
					Registration.id = Registration_Information.registrationId
				INNER JOIN
					ContactField
				ON
					ContactField.id = Registration_Information.contactFieldId
				WHERE
					Registration.eventId = :eventId
				AND
					ContactField.formInputId IN (1, 2)
				AND
					Registration_Information.value LIKE CONCAT(:searchTerm, '%')
			)
			UNION ALL
			(
				SELECT
					Registration.id as registrationId,
					Registration.regGroupId,
					ContactField.displayName,
					ContactFieldOption.displayName as value
				FROM
					Registration_Information
				INNER JOIN
					Registration
				ON
					Registration.id = Registration_Information.registrationId
				INNER JOIN
					ContactField
				ON
					ContactField.id = Registration_Information.contactFieldId
				INNER JOIN
					ContactFieldOption 
				ON
					ContactField.id = ContactFieldOption.contactFieldId
				WHERE
					Registration.eventId = :eventId
				AND
					ContactField.formInputId IN (3, 4, 5)
				AND
					ContactFieldOption.displayName LIKE CONCAT(:searchTerm, '%')
			)
		";
		
		return $this->rawQuery($sql, $params, 'Search registration information.');
	}
}

?>