<?php

class db_reg_RegistrationManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Registration';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		$obj['information'] = db_reg_InformationManager::getInstance()->findByRegistration($obj);
		$obj['regOptions'] = db_reg_RegOptionManager::getInstance()->findByRegistration($obj);
		$obj['variableQuantity'] = db_reg_VariableQuantityManager::getInstance()->findByregistration($obj);
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_reg_RegistrationManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				dateRegistered,
				comments,
				dateCancelled,
				regGroupId,
				categoryId,
				eventId,
				regTypeId
			FROM
				Registration
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find registration.');
	}
	
	/**
	 * creates a row in the registration table for the 
	 * given registration. this includes the associated
	 *  information, reg option, variable quantity, and payment rows. 
	 * @param $r
	 */
	public function createRegistration($regGroupId, $r) { 
		$sql = '
			INSERT INTO
				Registration(
					dateRegistered,
					comments,
					regGroupId,
					categoryId,
					eventId,
					regTypeId	
				)
			VALUES(
				:dateRegistered,
				:comments,
				:regGroupId,
				:categoryId,
				:eventId,
				:regTypeId
			)
		';
		
		$today = new DateTime();
		
		$params = array(
			'dateRegistered' => date_format($today,'Y-m-d H:i'),
			'comments' => '',
			'regGroupId' => $regGroupId,
			'categoryId' => $r['categoryId'],
			'eventId' => $r['eventId'],
			'regTypeId' => $r['regTypeId']
		);
		
		$this->execute($sql, $params, 'Create registration.');
		
		$regId = $this->lastInsertId();
		
		db_reg_InformationManager::getInstance()->createInformation($regId, $r['information']);
		
		db_reg_RegOptionManager::getInstance()->createOptions($r['regTypeId'], $regId, $r['regOptionIds']);
		
		db_reg_VariableQuantityManager::getInstance()->createOptions($r['regTypeId'], $regId, $r['variableQuantity']);
		
		// a registration will not have a payment if the event doesn't have one set up.
		if(!empty($r['paymentInfo']) && !empty($r['paymentInfo']['paymentType'])) {
			db_reg_PaymentManager::getInstance()->createPayment($regGroupId, $r['paymentInfo']);
		}
		
		return $regId;
	}
	
	public function createRegistrations($regs) {
		$ids = array();
		
		if(!empty($regs)) {
			$regGroupId = db_reg_GroupManager::getInstance()->createGroup();
			
			foreach($regs as $r) {
				$ids[] = $this->createRegistration($regGroupId, $r);
			}
		}
		
		return $ids;
	}
	
	public function findEventCount($event) {
		$sql = '
			SELECT
				count(*) as regCount
			FROM
				Registration
			WHERE
				dateCancelled is NULL
			AND
				eventId = :id
		';
		
		$params = array(
			'id' => $event['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find number registered for event.');
		
		return $result['regCount'];
	}
	
	public function findOptionCount($option) {
		$sql = '
			SELECT
				count(*) as regOptionCount
			FROM
				Registration
			INNER JOIN
				Registration_RegOption
			ON
				Registration.id = Registration_RegOption.registrationId
			WHERE
				Registration.dateCancelled is NULL
			AND
				Registration_RegOption.dateCancelled is NULL
			AND
				regOptionId = :regOptionId
		';
		
		$params = array(
			'regOptionId' => $option['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find number registered for reg option.');
		
		return $result['regOptionCount'];
	}
	
	public function findVariableOptionCount($option) {
		$sql = '
			SELECT
				count(*) as regOptionCount
			FROM
				Registration
			INNER JOIN
				Registration_VariableQuantityOption
			ON
				Registration.id = Registration_VariableQuantityOption.variableQuantityId
			WHERE
				Registration.dateCancelled is NULL
			AND
				Registration_VariableQuantityOption.dateCancelled is NULL
			AND
				variableQuantityId = :variableQuantityId
		';
		
		$params = array(
			'variableQuantityId' => $option['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find number registered for reg option.');
		
		return $result['regOptionCount'];
	}
	
	public function findByRegistrationGroup($group) {
		$sql = '
			SELECT
				id,
				dateRegistered,
				comments,
				dateCancelled,
				regGroupId,
				categoryId,
				eventId,
				regTypeId
			FROM
				Registration
			WHERE
				regGroupId = :regGroupId
		';
		
		$params = array(
			'regGroupId' => $group['id']
		);
		
		return $this->query($sql, $params, 'Find registrations by group.');
	}
}

?>