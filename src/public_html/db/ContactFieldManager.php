<?php

class db_ContactFieldManager extends db_OrderableManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'ContactField';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
			
		// set field's attributes.
		$attributes = db_AttributeManager::getInstance()->findContactFieldAttributes($obj);
		$obj['attributes'] = $attributes;

		// set field's validation rules.
		$validationRules = db_ValidationManager::getInstance()->findContactFieldRules($obj);
		$obj['validationRules'] = $validationRules;
		
		// set field's visibleTo/visibleToAll properties.
		$obj['visibleToAll'] = $this->isVisibleToAllRegTypes($obj);
		$regTypes = db_RegTypeManager::getInstance()->findRegTypesForField($obj);
		$obj['visibleTo'] = $regTypes;
		
		// set field's options (if any)
		$obj['options'] = db_ContactFieldOptionManager::getInstance()->findByField($obj);
	
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_ContactFieldManager();
		}
		
		return self::$instance;
	}
	
	public function findBySection($section) {
		$sql = '
			SELECT
				ContactField.id,
				ContactField.eventId,
				ContactField.sectionId,
				ContactField.code,
				ContactField.displayName,
				ContactField.displayOrder,
				FormInput.id as formInput_id,
				FormInput.name as formInput_name,
				FormInput.displayName as formInput_displayName
			FROM
				ContactField
			INNER JOIN
				FormInput
			ON
				ContactField.formInputId=FormInput.id
			WHERE
				ContactField.sectionId=:id
			ORDER BY
				ContactField.displayOrder				
		';
		
		$params = array(
			'id' => $section['id']
		);
		
		return $this->query($sql, $params, 'Find section contact fields.');
	}
	
	public function find($id) {
		$sql = '
			SELECT
				ContactField.id,
				ContactField.eventId,
				ContactField.sectionId,
				ContactField.code,
				ContactField.displayName,
				ContactField.displayOrder,
				FormInput.id as formInput_id,
				FormInput.name as formInput_name,
				FormInput.displayName as formInput_displayName
			FROM
				ContactField
			INNER JOIN
				FormInput
			ON
				ContactField.formInputId=FormInput.id
			WHERE
				ContactField.id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find contact field.');
	}
	
	public function createContactField($properties) {
		$sql = '
			INSERT INTO
				ContactField(
					eventId,
					sectionId,
					code,
					displayName,
					displayOrder,
					formInputId	
				)
			VALUES(
				:eventId,
				:sectionId,
				:code,
				:displayName,
				:displayOrder,
				:formInputId
			)
		';
		
		$params = array(
			'eventId' => $properties['eventId'],
			'sectionId' => $properties['sectionId'],
			'code' => $properties['code'],
			'displayName' => $properties['displayName'],
			'displayOrder' => $this->getNextOrder(),
			'formInputId' => $properties['formInputId']
		);
		
		$this->execute($sql, $params, 'Create contact field.');
		
		//
		// save contact field associations
		//
		
		$contactFieldId = $this->lastInsertId();
		
		// map to Attributes.
		$attributes = $properties['attributes'];
		$this->setAttributes($contactFieldId, $attributes);
		
		// map to Validation rules.
		$rules = $properties['validationRules'];
		$this->setValidationRules($contactFieldId, $rules);
		
		// map to RegType.
		$typeIds = $properties['regTypeIds'];
		$this->setRegTypes($contactFieldId, $typeIds);
	}
	
	private function isVisibleToAllRegTypes($field) {
		$sql = '
			SELECT
				contactFieldId
			FROM 
				RegTypeContactField
			WHERE
				contactFieldId=:id
			AND
				regTypeId is NULL
		';
		
		$params = array(
			'id' => $field['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if contact field is visible to all reg types.');

		return !empty($result);
	}
	
	private function setAttributes($contactFieldId, $attributes) {
		foreach($attributes as $id => $value) {
			if(is_numeric($id) && !empty($value)) {
				$sql = '
					INSERT INTO
						ContactFieldAttribute(
							contactFieldId,
							attributeId,
							attrValue
						)
					VALUES(
						:contactFieldId,
						:attributeId,
						:attrValue
					)
				';
			
				$params = array(
					'contactFieldId' => $contactFieldId,
					'attributeId' => $id,
					'attrValue' => $value
				);
			
				$this->execute($sql, $params, 'Set contact field attribute.');
			}
		}
	}
	
	private function setValidationRules($contactFieldId, $validationRules) {
		foreach($validationRules as $id => $value) {
			if(is_numeric($id) && !empty($value)) {
				$sql = '
					INSERT INTO
						ContactFieldValidation(
							contactFieldId,
							validationId,
							validationValue
						)
					VALUES(
						:contactFieldId,
						:validationId,
						:validationValue
					)
				';
					
				$params = array(
					'contactFieldId' => $contactFieldId,
					'validationId' => $id,
					'validationValue' => $value
				);
					
				$this->execute($sql, $params, 'Set contact field validation rules.');
			}
		}
	}
	
	private function setRegTypes($contactFieldId, $regTypeIds) {
		if(in_array(-1, $regTypeIds)) {
			// contact field visible to ALL reg types. so we
			// want the regTypeId to be NULL.
			$sql = '
				INSERT INTO
					RegTypeContactField(
						contactFieldId
					)
				VALUES(
					:contactFieldId
				)
			';
				
			$params = array(
				'contactFieldId' => $contactFieldId
			);
			
			$this->execute($sql, $params, 'Set contact field visible to reg types.');
		}
		else {
			foreach($regTypeIds as $regTypeId) {
				$sql = '
					INSERT INTO
						RegTypeContactField(
							regTypeId,
							contactFieldId
						)
					VALUES(
						:regTypeId,
						:contactFieldId
					)
				';
			
				$params = array(
					'regTypeId' => $regTypeId,
					'contactFieldId' => $contactFieldId
				);
			
				$this->execute($sql, $params, 'Set contact field visible to reg types.');
			}
		}		
	}
	
	public function save($field) {
		$this->removeAttributes($field);
		$this->removeValidationRules($field);
		$this->removeRegTypes($field);
		
		// remove options if necessary.
		$inputType = intval($field['formInputId'], 10);
		$needsOptions = $inputType === model_FormInput::$CHECKBOX || 
						$inputType === model_FormInput::$RADIO || 
						$inputType === model_FormInput::$SELECT; 
		if(!$needsOptions) {
			db_ContactFieldOptionManager::getInstance()->removeOptions($field);
		}
		
		$sql = '
			UPDATE
				ContactField
			SET
				code=:code,
				formInputId=:formInputId,
				displayName=:displayName
			WHERE
				id=:id
		';

		$params = array(
			'id' => $field['id'],
			'code' => $field['code'],
			'formInputId' => $field['formInputId'],
			'displayName' => $field['displayName']
		);
		
		$this->execute($sql, $params, 'Save contact field.');
		
		//
		// save contact field associations
		//
			
		$contactFieldId = $field['id'];
		
		// map to Attributes.
		$this->setAttributes($contactFieldId, $field['attributes']);
		
		// map to Validation rules.
		$this->setValidationRules($contactFieldId, $field['validationRules']);
		
		// map to RegType.
		$this->setRegTypes($contactFieldId, $field['regTypeIds']);
	}
	
	public function removeAttributes($field) {
		$sql = '
			DELETE FROM
				ContactFieldAttribute
			WHERE
				contactFieldId=:id
		';
		
		$params = array(
			'id' => $field['id']
		);
		
		$this->execute($sql, $params, 'Remove contact field attributes.');
	}
	
	public function removeValidationRules($field) {
		$sql = '
			DELETE FROM
				ContactFieldValidation
			WHERE
				contactFieldId=:id
		';
		
		$params = array(
			'id' => $field['id']
		);
		
		$this->execute($sql, $params, 'Remove contact field validation rules.');
	}
	
	public function removeRegTypes($field) {
		$sql = '
			DELETE FROM
				RegTypeContactField
			WHERE
				contactFieldId=:id
		';
		
		$params = array(
			'id' => $field['id']
		);
		
		$this->execute($sql, $params, 'Remove contact field from reg types.');
	}
	
	public function delete($field) {
		$sql = '
			DELETE FROM
				ContactField
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $field['id']
		);
		
		$this->execute($sql, $params, 'Delete contact field.');
	}
	
	public function moveFieldUp($field) {
		$this->moveUp($field, 'sectionId', $field['sectionId']);
	}
	
	public function moveFieldDown($field) {
		$this->moveDown($field, 'sectionId', $field['sectionId']);
	}
}

?>