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
		$regTypes = db_RegTypeManager::getInstance()->findRegTypesForField(array(
			'eventId' => $obj['eventId'],
			'contactFieldId' => $obj['id'],
			'sectionId' => $obj['sectionId'],
			'visibleToAll' => $obj['visibleToAll']
		));
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
	
	/**
	 * 
	 * @param array $params [eventId, sectionId]
	 */
	public function findBySection($params) {
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
				ContactField.formInputId = FormInput.id
			WHERE
				ContactField.sectionId = :sectionId
			AND
				ContactField.eventId = :eventId
			ORDER BY
				ContactField.displayOrder				
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'sectionId'));
		
		return $this->query($sql, $params, 'Find section contact fields.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
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
				ContactField.formInputId = FormInput.id
			WHERE
				ContactField.id = :id
			AND
				ContactField.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find contact field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, sectionId, code, displayName, formInputId, 
	 * 						 attributes, validationRules, regTypeIds]
	 */
	public function createContactField($params) {
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
		
		$p = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'sectionId',
			'code',
			'displayName',
			'formInputId'
		));
		$p['displayOrder'] = $this->getNextOrder();
		
		$this->execute($sql, $p, 'Create contact field.');
		
		//
		// save contact field associations
		//
		
		$contactFieldId = $this->lastInsertId();
		
		// map to Attributes.
		$attributes = $params['attributes'];
		$this->setAttributes(array(
			'eventId' => $params['eventId'],
			'contactFieldId' => $contactFieldId, 
			'attributes' => $attributes
		));
		
		// map to Validation rules.
		$rules = $params['validationRules'];
		$this->setValidationRules(array(
			'eventId' => $params['eventId'],
			'contactFieldId' => $contactFieldId, 
			'validationRules' => $rules
		));
		
		// map to RegType.
		$typeIds = $params['regTypeIds'];
		$this->setRegTypes(array(
			'eventId' => $params['eventId'],
			'contactFieldId' => $contactFieldId, 
			'regTypeIds' => $typeIds
		));
		
		return $contactFieldId;
	}
	
	/**
	 * 
	 * @param array $params [id]
	 */
	private function isVisibleToAllRegTypes($params) {
		$sql = '
			SELECT
				contactFieldId
			FROM 
				RegTypeContactField
			WHERE
				contactFieldId = :id
			AND
				regTypeId IS NULL
		';
		
		$params = array(
			'id' => $params['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if contact field is visible to all reg types.');

		return !empty($result);
	}
	
	/**
	 * 
	 * @param array $params [eventId, contactFildId, attributes]
	 */
	private function setAttributes($params) {
		$this->checkContactFieldPermission(array(
			'eventId' => $params['eventId'],
			'id' => $params['contactFieldId']
		));
		
		foreach($params['attributes'] as $id => $value) {
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
					'contactFieldId' => $params['contactFieldId'],
					'attributeId' => $id,
					'attrValue' => $value
				);
			
				$this->execute($sql, $params, 'Set contact field attribute.');
			}
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, contactFieldId, validationRules]
	 */
	private function setValidationRules($params) {
		$this->checkContactFieldPermission(array(
			'eventId' => $params['eventId'],
			'id' => $params['contactFieldId']
		));
		
		foreach($$params['validationRules'] as $id => $value) {
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
					'contactFieldId' => $$params['contactFieldId'],
					'validationId' => $id,
					'validationValue' => $value
				);
					
				$this->execute($sql, $params, 'Set contact field validation rules.');
			}
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, contactFieldId, regTypeIds]
	 */
	private function setRegTypes($params) {
		$this->checkContactFieldPermission(array(
			'eventId' => $params['eventId'],
			'id' => $params['contactFieldId']
		));
		
		if(in_array(-1, $params['regTypeIds'])) {
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
				
			$p = array(
				'contactFieldId' => $params['contactFieldId']
			);
			
			$this->execute($sql, $p, 'Set contact field visible to reg types.');
		}
		else {
			foreach($params['regTypeIds'] as $regTypeId) {
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
			
				$p = array(
					'regTypeId' => $regTypeId,
					'contactFieldId' => $params['contactFieldId']
				);
			
				$this->execute($sql, $p, 'Set contact field visible to reg types.');
			}
		}		
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, code, displayName, formInputId, attributes, validationRules, regTypeIds]
	 */
	public function save($params) {
		$this->removeAttributes($params);
		$this->removeValidationRules($params);
		$this->removeRegTypes($params);
		
		// remove options if necessary.
		$inputType = intval($params['formInputId'], 10);
		$needsOptions = $inputType === model_FormInput::$CHECKBOX || 
						$inputType === model_FormInput::$RADIO || 
						$inputType === model_FormInput::$SELECT; 
		if(!$needsOptions) {
			db_ContactFieldOptionManager::getInstance()->removeOptions($params);
		}
		
		$sql = '
			UPDATE
				ContactField
			SET
				code = :code,
				formInputId = :formInputId,
				displayName = :displayName
			WHERE
				id = :id
			AND
				eventId = :eventId
		';

		$p = ArrayUtil::keyIntersect($params, array('eventId', 'id', 'code', 'displayName', 'formInputId'));
		
		$this->execute($sql, $p, 'Save contact field.');
		
		//
		// save contact field associations
		//
			
		$contactFieldId = $field['id'];
		
		// map to Attributes.
		$this->setAttributes(array(
			'eventId' => $params['eventId'],
			'contactFieldId' => $params['id'],
			'attributes' => $params['attributes']
		));
		
		// map to Validation rules.
		$this->setValidationRules(array(
			'eventId' => $params['eventId'],
			'contactFieldId' => $params['id'],
			'validationRules' => $params['validationRules']
		));
		
		// map to RegType.
		$this->setRegTypes(array(
			'eventId' => $params['eventId'],
			'contactFieldId' => $params['id'],
			'regTypeIds' => $params['regTypeIds']
		));
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function removeAttributes($params) {
		$sql = '
			DELETE FROM
				ContactFieldAttribute
			WHERE
				ContactFieldAttribute.contactFieldId = :id
			AND
				ContactFieldAttribute.contactFieldId
			IN (
				SELECT ContactField.id
				FROM ContactField
				WHERE ContactField.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Remove contact field attributes.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function removeValidationRules($params) {
		$sql = '
			DELETE FROM
				ContactFieldValidation
			WHERE
				ContactFieldValidation.contactFieldId = :id
			AND
				ContactFieldValidation.contactFieldId
			IN (
				SELECT ContactField.id
				FROM ContactField
				WHERE ContactField.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Remove contact field validation rules.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function removeRegTypes($params) {
		$sql = '
			DELETE FROM
				RegTypeContactField
			WHERE
				RegTypeContactField.contactFieldId = :id
			AND
				RegTypeContactField.contactFieldId 
			IN (
				SELECT ContactField.id
				FROM ContactField
				WHERE ContactField.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Remove contact field from reg types.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function delete($params) {
		////////////////////////////////////////////////////////////////////
		// delete attributes.
		$this->removeAttributes($params);
		
		////////////////////////////////////////////////////////////////////
		// delete validation rules.
		$this->removeValidationRules($params);
		
		////////////////////////////////////////////////////////////////////
		// delete reg type associations.
		$this->removeRegTypes($params);
		
		////////////////////////////////////////////////////////////////////
		// delete field options.
		db_ContactFieldOptionManager::getInstance()->removeOptions($params);
		
		//////////////////////////////////////////////////////////////////////
		// delete group registration associations.
		$sql = '
			DELETE FROM
				GroupRegistration_ContactField
			WHERE
				GroupRegistration_ContactField.contactFieldId = :contactFieldId
			AND
				GroupRegistration_ContactField.contactFieldId
			IN (
				SELECT ContactField.id
				FROM ContactField
				WHERE ContactField.eventId = :eventId
			)
		';
		
		$p = array(
			'eventId' => $params['eventId'],
			'contactFieldId' => $params['id']
		);
		
		$this->execute($sql, $p, 'Delete group registration associations.');
		
		////////////////////////////////////////////////////////////////////
		// delete report associations.
		$sql = '
			DELETE FROM
				Report_ContactField
			WHERE
				Report_ContactField.contactFieldId = :contactFieldId
			AND
				Report_ContactField.contactFieldId 
			IN (
				SELECT ContactField.id
				FROM ContactField
				WHERE ContactField.eventId = :eventId
			)
		';
		
		$p = array(
			'eventId' => $params['eventId'],
			'contactFieldId' => $params['id']
		);
		
		$this->execute($sql, $p, 'Delete report associations.');
		
		////////////////////////////////////////////////////////////////////
		// delete field.
		$sql = '
			DELETE FROM
				ContactField
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete contact field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, sectionId]
	 */
	public function moveFieldUp($params) {
		$this->checkContactFieldPermission($params);
		
		$this->moveUp($params, 'sectionId', $params['sectionId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, sectionId]
	 */
	public function moveFieldDown($params) {
		$this->checkContactFieldPermission($params);
		
		$this->moveDown($params, 'sectionId', $params['sectionId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findTextFieldsByEventId($params) {
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
				ContactField.formInputId = FormInput.id
			WHERE
				ContactField.eventId = :eventId
			AND
				ContactField.formInputId = :formInputId
			ORDER BY
				ContactField.displayOrder
				
		';
		
		$params = array(
			'eventId' => $params['eventId'],
			'formInputId' => model_FormInput::$TEXT
		);
		
		return $this->query($sql, $params, 'Find text fields by event.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	private function checkContactFieldPermission($params) {
		$sql = '
			SELECT
				id,
				eventId
			FROM
				ContactField
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$results = $this->rawQuery($sql, $params, 'Check contact field permission.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to ContactField: (event id, contact field id) -> ({$params['eventId']}, {$params['id']}).");
		}
	}
}

?>