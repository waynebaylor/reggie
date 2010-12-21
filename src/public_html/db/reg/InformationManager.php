<?php

class db_reg_InformationManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		'Registration_Information';
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
		
		// convert field values to an array. if a field allows multiple 
		// values (checkbox) they will be all be put in the array.
		$infos = array();
		foreach($results as $result) {
			$fieldId = $result['contactFieldId'];
			$value = $result['value'];
			
			if(empty($infos[$fieldId])) {
				$infos[$fieldId] = $result;
				$infos[$fieldId]['value'] = array();
			}
			
			$infos[$fieldId]['value'][] = $value;
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
}

?>