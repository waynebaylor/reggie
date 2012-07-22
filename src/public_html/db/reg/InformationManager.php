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
	
	/**
	 * 
	 * @param array $params [eventId, registrationId, sectionId]
	 */
	public function deleteBySection($params) {
		$fields = db_ContactFieldManager::getInstance()->findBySection($params);
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
				'registrationId' => $params['registrationId'],
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
		$extraSql = '
			,
			R.dateRegistered,
			R.dateCancelled,
			(
		        select RI2.value 
		        from Registration_Information RI2
		        where RI2.contactFieldId=421 
		        and RI2.registrationId = R.id
		    ) as firstName,
		    (
		        select RI2.value 
		        from Registration_Information RI2
		        where RI2.contactFieldId=422
		        and RI2.registrationId = R.id
		    ) as lastName,
		    (
		        select RI2.value 
		        from Registration_Information RI2
		        where RI2.contactFieldId=423
		        and RI2.registrationId = R.id
		    ) as email
		';	
		
		$sql = "
			(
				SELECT
					R.id as registrationId,
					R.regGroupId,
					RI.value as value, 
					CF.displayName
					{$extraSql}
				FROM
					Registration_Information RI
				INNER JOIN
					Registration R
				ON
					R.id = RI.registrationId
				INNER JOIN
					ContactField CF
				ON
					CF.id = RI.contactFieldId
				WHERE
					R.eventId = :eventId
				AND
					CF.formInputId IN (1, 2)
				AND
					RI.value LIKE CONCAT(:searchTerm, '%')
			)
			UNION ALL
			(
				SELECT
					R.id as registrationId,
					R.regGroupId,
					CF.displayName,
					CFO.displayName as value
					{$extraSql}
				FROM
					Registration_Information RI
				INNER JOIN
					Registration R
				ON
					R.id = RI.registrationId
				INNER JOIN
					ContactField CF
				ON
					CF.id = RI.contactFieldId
				INNER JOIN
					ContactFieldOption CFO
				ON
					CF.id = CFO.contactFieldId
				WHERE
					R.eventId = :eventId
				AND
					CF.formInputId IN (3, 4, 5)
				AND
					CFO.displayName LIKE CONCAT(:searchTerm, '%')
			)
		";
		
		$params = ArrayUtil::keyIntersect($params, array('searchTerm', 'eventId'));
		
		return $this->rawQuery($sql, $params, 'Search registration information.');
	}
}

?>