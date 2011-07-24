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
		return $this->findRegOptionCost($id) + $this->findVariableQuantityCost($id);
	}
	
	private function findRegOptionCost($groupId) {
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
 			AND
 				Registration_RegOption.dateCancelled IS NULL
		';
		
		$params = array(
			'id' => $groupId
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find reg option cost for registration group.');
		
		return $result['total_cost'];
	}
	
	private function findVariableQuantityCost($groupId) {
		$sql = '
			SELECT 
 				sum(RegOptionPrice.price*Registration_VariableQuantityOption.quantity) as total_cost
			FROM 
 				Registration
			INNER JOIN
 				RegistrationGroup
			ON
 				Registration.regGroupId = RegistrationGroup.id 
			INNER JOIN
 				Registration_VariableQuantityOption
			ON
 				Registration.id = Registration_VariableQuantityOption.registrationId
			INNER JOIN 
 				RegOptionPrice
			ON
 				Registration_VariableQuantityOption.priceId = RegOptionPrice.id
			WHERE
 				RegistrationGroup.id = :id
		';
		
		$params = array(
			'id' => $groupId
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find variable quantity cost for registration group.');
		
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
			AND
				paymentReceived = :paymentReceived
		';
		
		$params = array(
			'id' => $id,
			'paymentReceived' => 'T'
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find total paid by registration group.');
		
		return $result['total_paid'];
	}
	
	public function findByEventId($eventId) {
		$sql = '
			SELECT DISTINCT 
				regGroupId as id
			FROM
				Registration 
			WHERE
				eventId = :eventId
		';
		
		$params = array(
			'eventId' => $eventId
		);
		
		return $this->query($sql, $params, 'Find registration groups by event.');
	}
	
	public function findEventInfoByGroupId($id) {
		$sql = '
			SELECT
				RegistrationGroup.id,
				Registration.eventId 
			FROM
				RegistrationGroup
			INNER JOIN
				Registration
			ON
				RegistrationGroup.id = Registration.regGroupId
			WHERE
				RegistrationGroup.id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find event id by reg group.');
		
		return db_EventManager::getInstance()->findInfoById($result['eventId']);
	}
}

?>