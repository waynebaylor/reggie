<?php

class db_reg_GroupManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		$obj['registrations'] = db_reg_RegistrationManager::getInstance()->findByRegistrationGroup($obj);
		$obj['payments'] = db_reg_PaymentManager::getInstance()->findByRegistrationGroup($obj);
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_reg_GroupManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT 
				id
			FROM
				RegistrationGroup
			WHERE
				id = :id
		';

		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find registration group.');
	}
	
	public function createGroup() {
		$sql = '
			INSERT INTO
				RegistrationGroup()
			VALUES()
		';

		$this->execute($sql, array(), 'Create registration group.');
		
		return $this->lastInsertId();
	}
	
	public function findTotalCost($id) {
		$sql = '
			SELECT 
 				sum(RegOptionPrice.price) as total_cost
			FROM 
 				Registration
			INNER JOIN
 				RegistrationGroup
			ON
 				Registration.regGroupId = RegistrationGroup.id 
			INNER JOIN
 				Registration_RegOption
			ON
 				Registration.id = Registration_RegOption.registrationId
			INNER JOIN 
 				RegOptionPrice
			ON
 				Registration_RegOption.priceId = RegOptionPrice.id
			WHERE
 				RegistrationGroup.id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find total cost for registration group.');
		
		return $result['total_cost'];
	}
	
	public function findTotalPaid($id) {
		$sql = '
			SELECT
				sum(amount) as total_paid
			FROM
				Payment
			WHERE
				regGroupId = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find total paid by registration group.');
		
		return $result['total_paid'];
	}
}

?>