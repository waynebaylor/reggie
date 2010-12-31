<?php

class db_FormInputManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public function getTableName() {
		return 'FormInput';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		$obj['attributes'] = db_AttributeManager::getInstance()->findFormInputAttributes($obj);
		$obj['validationRules'] = db_ValidationManager::getInstance()->findFormInputRules($obj);
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_FormInputManager();
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
				FormInput
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		$this->queryUnique($sql, $params, 'Find form input.');
	}
	
	public function findAll() {
		$sql = '
			SELECT
				id,
				name,
				displayName
			FROM
				FormInput
		';
		
		return $this->query($sql, array(), 'Find all form inputs.');
	}
}

?>