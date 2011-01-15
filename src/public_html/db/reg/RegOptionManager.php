<?php

class db_reg_RegOptionManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
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
				dateCancelled,
				dateAdded
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
			
			$this->createOption($regId, $option['id'], $price['id']);
		}
	}
	
	public function createOption($registrationId, $optionId, $priceId) {
			$sql = '
				INSERT INTO
					Registration_RegOption(
						registrationId,
						regOptionId,
						priceId,
						dateAdded
					)
				VALUES(
					:registrationId,
					:regOptionId,
					:priceId,
					:dateAdded
				)
			';
				
			$params = array(
				'registrationId' => $registrationId,
				'regOptionId' => $optionId,
				'priceId' => $priceId,
				'dateAdded' => date(db_Manager::$DATE_FORMAT)
			);
				
			$this->execute($sql, $params, 'Create registration option.');
	}
	
	public function cancel($id) {
		$sql = '
			UPDATE
				Registration_RegOption
			SET
				dateCancelled = :dateCancelled
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $id,
			'dateCancelled' => date(db_Manager::$DATE_FORMAT)
		);
		
		$this->execute($sql, $params, 'Cancel registration option.');
	}
}

?>