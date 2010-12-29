<?php

class db_reg_VariableQuantityManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Registration_VariableQuantityOption';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_reg_VariableQuantityManager();
		}
		
		return self::$instance;
	}
	
	public function findByRegistration($reg) {
		$sql = '
			SELECT
				id,
				registrationId,
				variableQuantityId,
				priceId,
				quantity,
				comments
			FROM
				Registration_VariableQuantityOption
			WHERE
				registrationId = :registrationId
		';
		
		$params = array(
			'registrationId' => $reg['id']
		);
		
		return $this->query($sql, $params, 'Find variable quantity options by registration.');
	}
	
	public function createOptions($regTypeId, $registrationId, $options) {
		foreach($options as $opt) {
			$option = db_VariableQuantityOptionManager::getInstance()->find($opt['id']);
			$price = model_RegOption::getPrice(array('id' => $regTypeId), $option);
				
			$this->createOption(array(
				'registrationId' => $registrationId,
				'variableQuantityId' => $opt['id'],
				'priceId' => $price['id'],
				'quantity' => $opt['quantity'],
				'comments' => ''
			));
		}
	}
	
	public function delete($registrationId, $optionId) {
		$sql = '
			DELETE FROM
				Registration_VariableQuantityOption
			WHERE
				registrationId = :registrationId
			AND
				variableQuantityId = :variableQuantityId
		';
		
		$params = array(
			'registrationId' => $registrationId,
			'variableQuantityId' => $optionId
		);
		
		$this->execute($sql, $params, 'Delete variable quantity registration option.');
	}
	
	public function save($option) {
		$sql = '
			UPDATE
				Registration_VariableQuantityOption
			SET
				priceId = :priceId,
				quantity = :quantity,
				comments = :comments
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $option['id'],
			'priceId' => $option['priceId'],
			'quantity' => $option['quantity'],
			'comments' => $option['comments']
		);
		
		$this->execute($sql, $params, 'Save variable quantity registration option.');
	}
	
	public function createOption($option) {
		$sql = '
			INSERT INTO
				Registration_VariableQuantityOption(
					registrationId,
					variableQuantityId,
					priceId,
					quantity,
					comments
				)
			VALUES(
				:registrationId,
				:variableQuantityId,
				:priceId,
				:quantity,
				:comments
			)
		';
			
		$params = array(
			'registrationId' => $option['registrationId'],
			'variableQuantityId' => $option['variableQuantityId'],
			'priceId' => $option['priceId'],
			'quantity' => $option['quantity'],
			'comments' => $option['comments']
		);
		
		$this->execute($sql, $params, 'Create variable quantity registration option.');
	}
}

?>