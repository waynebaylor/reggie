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
		
	}
	
	public function createOptions($regTypeId, $registrationId, $options) {
		foreach($options as $opt) {
			$option = db_VariableQuantityOptionManager::getInstance()->find($opt['id']);
			$price = model_RegOption::getPrice(array('id' => $regTypeId), $option);
				
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
				'registrationId' => $registrationId,
				'variableQuantityId' => $opt['id'],
				'priceId' => $price['id'],
				'quantity' => $opt['quantity'],
				'comments' => ''
			);
			
			$this->execute($sql, $params, 'Create variable quantity registration option.');
		}
	}
}

?>