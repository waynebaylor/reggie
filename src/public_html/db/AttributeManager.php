<?php

class db_AttributeManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public function getTableName() {
		return 'Attribute';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_AttributeManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				name,
				displayName
			FROM
				Attribute
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find attribute.');
	}
	
	public function findAll() {
		$sql = '
			SELECT
				id,
				name,
				displayName
			FROM
				Attribute
		';
		
		return $this->query($sql, array(), 'Find all attributes.');
	}
	
	public function findContactFieldAttributes($contactField) {
		$sql = '
			SELECT
				Attribute.id,
				Attribute.name,
				Attribute.displayName,
				ContactFieldAttribute.attrValue as value
			FROM
				ContactFieldAttribute
			INNER JOIN
				Attribute
			ON
				ContactFieldAttribute.attributeId=Attribute.id
			WHERE
				ContactFieldAttribute.contactFieldId=:id
		';
		
		$params = array(
			'id' => $contactField['id']
		);
		
		return $this->query($sql, $params, 'Find contact field attributes.');
	}
	
	public function findFormInputAttributes($formInput) {
		$sql = '
			SELECT
				Attribute.id,
				Attribute.name,
				Attribute.displayName
			FROM
				Attribute
			INNER JOIN
				FormInputAttribute
			ON
				FormInputAttribute.attributeId=Attribute.id
			WHERE
				FormInputAttribute.formInputId=:id
		';
		
		$params = array(
			'id' => $formInput['id']
		);
		
		return $this->query($sql, $params, 'Find form input attributes.');
	}
}

?>