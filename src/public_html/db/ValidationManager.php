<?php

class db_ValidationManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public function getTableName() {
		return 'Validation';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_ValidationManager();
		}
		
		return self::$instance;
	}
	
	public function findAll() {
		$sql = '
			SELECT
				id,
				name,
				displayName
			FROM
				Validation
		';
		
		return $this->query($sql, array(), 'Find all validation rules.');
	}
	
	public function findContactFieldRules($contactField) {
		$sql = '
			SELECT
				Validation.id,
				Validation.name,
				Validation.displayName,
				ContactFieldValidation.validationValue as value
			FROM
				ContactFieldValidation
			INNER JOIN
				Validation
			ON
				ContactFieldValidation.validationId=Validation.id
			WHERE
				ContactFieldValidation.contactFieldId=:id
		';

		$params = array(
			'id' => $contactField['id']
		);
		
		return $this->query($sql, $params, 'Find contact field validation rules.');
	}
	
	public function findFormInputRules($formInput) {
		$sql = '
			SELECT
				Validation.id,
				Validation.name,
				Validation.displayName
			FROM
				Validation
			INNER JOIN
				FormInputValidation
			ON
				FormInputValidation.validationId=Validation.id
			WHERE
				FormInputValidation.formInputId=:id
		';

		$params = array(
			'id' => $formInput['id']
		);
		
		return $this->query($sql, $params, 'Find form input validation rules.');
	}
}

?>