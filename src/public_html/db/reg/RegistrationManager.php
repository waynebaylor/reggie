<?php

class db_reg_RegistrationManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
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
		
		return $regId;
	}
	
	public function createRegistrations($regs, $payment) {
		$ids = array();
		
		if(!empty($regs)) {
			$regGroupId = db_reg_GroupManager::getInstance()->createGroup();
				
			// may not have a payment if zero due or event doesn't have any payment types enabled.
			if(!empty($payment)) {
				db_reg_PaymentManager::getInstance()->createPayment($regGroupId, $payment);
			}

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
	
	public function save($registration) {
		$sql = '
			UPDATE
				Registration
			SET
				comments = :comments
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $registration['id'],
			'comments' => $registration['comments']
		);
		
		$this->execute($sql, $params, 'Save registration.');
	}
	
	public function cancelRegistration($registration) {
		// 1. mark reg as cancelled
		$sql = '
			UPDATE
				Registration
			SET
				dateCancelled = :dateCancelled
			WHERE
				id = :id
			AND
				dateCancelled IS NULL
				
		';
		
		$params = array(
			'id' => $registration['id'],
			'dateCancelled' => date(db_Manager::$DATE_FORMAT)
		);
		
		$this->execute($sql, $params, 'Cancel registration.');
		
		// 2. mark all reg options as cancelled
		foreach($registration['regOptions'] as $opt) {
			db_reg_RegOptionManager::getInstance()->cancel($opt['id']);	
		}
		
		// 3. remove var quantity amounts
		foreach($registration['variableQuantity'] as $varQuantity) {
			db_reg_VariableQuantityManager::getInstance()->delete($varQuantity['registrationId'], $varQuantity['variableQuantityId']);
		}
	}
	
	public function changeRegType($registration, $newRegTypeId) {
		// 1. set new reg type.
		$sql = '
			UPDATE
				Registration
			SET
				regTypeId = :regTypeId
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $registration['id'],
			'regTypeId' => $newRegTypeId
		);
		
		$this->execute($sql, $params, 'Change reg type.');
		
		// 2. remove irrelevant information fields.
		db_reg_InformationManager::getInstance()->retainFieldsByRegType($registration['id'], $newRegTypeId);
		
		// 3. cancel all reg options.
		foreach($registration['regOptions'] as $opt) {
			db_reg_RegOptionManager::getInstance()->cancel($opt['id']);	
		}
		
		// 4. remove all variable quantity options.
		foreach($registration['variableQuantity'] as $varQuantity) {
			db_reg_VariableQuantityManager::getInstance()->delete($varQuantity['registrationId'], $varQuantity['variableQuantityId']);
		}
	}
	
	public function findRegOptionCost($registration) {
		$sql = '
			SELECT
				sum(RegOptionPrice.price) as total_cost
			FROM
				Registration
			INNER JOIN
				Registration_RegOption
			ON
 				Registration.id = Registration_RegOption.registrationId
			INNER JOIN 
 				RegOptionPrice
			ON
 				Registration_RegOption.priceId = RegOptionPrice.id
 			WHERE
 				Registration.id = :id
 			AND
 				Registration_RegOption.dateCancelled IS NULL
		';
		
		$params = array(
			'id' => $registration['id']);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find registration option cost.');
		
		return $result['total_cost'];
	}
	
	public function findVariableOptionCost($registration) {
		$sql = '
			SELECT 
 				sum(RegOptionPrice.price*Registration_VariableQuantityOption.quantity) as total_cost
			FROM 
 				Registration
			INNER JOIN
 				Registration_VariableQuantityOption
			ON
 				Registration.id = Registration_VariableQuantityOption.registrationId
			INNER JOIN 
 				RegOptionPrice
			ON
 				Registration_VariableQuantityOption.priceId = RegOptionPrice.id
			WHERE
 				Registration.id = :id
		';
		
		$params = array(
			'id' => $registration['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find variable option cost.');
		
		return $result['total_cost'];
	}
	
	public function findTotalCost($registration) {
		return $this->findRegOptionCost($registration) + $this->findVariableOptionCost($registration);
	}
}

?>