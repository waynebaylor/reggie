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
		$params = ArrayUtil::keyIntersect($params, array('searchTerm', 'eventId'));
		
		$extraSql = '';
		
		// if set, add metadata info to SQL. 
		$eventMetadata = db_EventMetadataManager::getInstance()->findMetadataByEventId($params['eventId']);
		foreach($eventMetadata as $m) {
			if($m['metadata'] === db_EventMetadataManager::$FIRST_NAME) {
				$extraSql .= '
					,
					(
				        select RI2.value 
				        from Registration_Information RI2
				        where RI2.contactFieldId = :firstNameFieldId
				        and RI2.registrationId = R.id
				    ) as firstName
		    	';
				
				$params['firstNameFieldId'] = $m['contactFieldId'];
			}	
			else if($m['metadata'] === db_EventMetadataManager::$LAST_NAME) {
				$extraSql .= '
					,
				    (
				        select RI2.value 
				        from Registration_Information RI2
				        where RI2.contactFieldId = :lastNameFieldId
				        and RI2.registrationId = R.id
				    ) as lastName
				';
				
				$params['lastNameFieldId'] = $m['contactFieldId'];
			}
			else if($m['metadata'] === db_EventMetadataManager::$EMAIL) {
				$extraSql .= '
					,
				    (
				        select RI2.value 
				        from Registration_Information RI2
				        where RI2.contactFieldId = :emailFieldId
				        and RI2.registrationId = R.id
				    ) as email
				';
				
				$params['emailFieldId'] = $m['contactFieldId'];
			}
		}
		
		$sql = "
			(
				SELECT
					R.id as registrationId,
					R.regGroupId,
					CF.displayName,
					RI.value as value,
					R.dateRegistered,
					R.dateCancelled
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
					CFO.displayName as value,
					R.dateRegistered,
					R.dateCancelled
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
			UNION ALL (
				SELECT 
					R.id as registrationId,
					R.regGroupId,
					'Lead Number' as displayName,
					R.leadNumber as value,
					R.dateRegistered,
					R.dateCancelled
					{$extraSql}
				FROM
					Registration R
				WHERE
					R.eventId = :eventId
				AND
					R.leadNumber = :searchTerm
			)
		";
		
		return $this->rawQuery($sql, $params, 'Search registration information.');
	}
}

?>