<?php

class db_reg_RegOptionManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		'Registration_RegOption';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_reg_RegOptionManager();
		}
		
		return self::$instance;
	}
	
	public function findByRegistration($r) {
		$sql = '
			SELECT
				id,
				registrationId,
				regOptionId,
				priceId,
				comments,
				dateCancelled
			FROM
				Registration_RegOption
			WHERE
				registrationId=:id
		';
		
		$params = array(
			'id' => $r['id']
		);
		
		return $this->query($sql, $params, 'Find registration reg options.');
	}
	
	/**
	 * creates the reg option rows associated with the given 
	 * reg id.
	 * 
	 * @param $id
	 * @param $optionIds
	 */
	public function createOptions($regTypeId, $regId, $optionIds) {
		foreach($optionIds as $optionId) {
			$option = db_RegOptionManager::getInstance()->find($optionId);
			$price = model_RegOption::getPrice(array('id' => $regTypeId), $option);
			
			$sql = '
				INSERT INTO
					Registration_RegOption(
						registrationId,
						regOptionId,
						priceId,
						comments
					)
				VALUES(
					:registrationId,
					:regOptionId,
					:priceId,
					:comments
				)
			';
				
			$params = array(
				'registrationId' => $regId,
				'regOptionId' => $optionId,
				'priceId' => $price['id'],
				'comments' => ''
			);
				
			$this->execute($sql, $params, 'Create registration option.');
		}
	}
}

?>