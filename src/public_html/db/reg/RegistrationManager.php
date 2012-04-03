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
				regTypeId,
				confirmationNumber,
				leadNumber
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
	
	public function findByEventId($eventId) {
		$sql = '
			SELECT
				id,
				dateRegistered,
				comments,
				dateCancelled,
				regGroupId,
				categoryId,
				eventId,
				regTypeId,
				confirmationNumber,
				leadNumber
			FROM
				Registration
			WHERE
				eventId = :eventId
		';
		
		$params = array(
			'eventId' => $eventId
		);
		
		return $this->query($sql, $params, 'Find registrations by event.');
	}
	
	private function getNewRegistrationInfo($regId) {
		$reg = self::getInstance()->find($regId);
		
		// get the contact field ids included in the event's group reg configuration.
		$groupRegInfoFieldIds = array();
		$groupRegData = db_GroupRegistrationManager::getInstance()->findByEventId($reg);
		foreach($groupRegData['fields'] as $groupRegField) {
			$groupRegInfoFieldIds[] = $groupRegField['contactFieldId'];
		}
		
		// get the group reg values from the sample registrant.
		$infoValues = array();
		foreach($reg['information'] as $info) {
			if(in_array($info['contactFieldId'], $groupRegInfoFieldIds)) {
				$infoValues[] = array('id' => $info['contactFieldId'], 'value' => $info['value']);
			}
		}
		
		return $infoValues;
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
					regTypeId,
					confirmationNumber
				)
			VALUES(
				:dateRegistered,
				:comments,
				:regGroupId,
				:categoryId,
				:eventId,
				:regTypeId,
				:confirmationNumber
			)
		';
		
		$today = new DateTime();
		
		$params = ArrayUtil::getValues($r, array(
			'regGroupId' => 0,
			'categoryId' => 0,
			'eventId' => 0,
			'regTypeId' => 0,
			'dateRegistered' => date_format($today,'Y-m-d H:i'),
			'comments' => '',
			'confirmationNumber' => '00000000'
		));
		$params['regGroupId'] = $regGroupId;

		$this->execute($sql, $params, 'Create registration.');
		
		$regId = $this->lastInsertId();
		
		//
		// set the confirmation number based on the DB id.
		//
		$sql = '
			UPDATE
				Registration
			SET
				confirmationNumber = :confirmationNumber
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $regId,
			'confirmationNumber' => 1000 + intval($regId, 10)
		);
		
		$this->execute($sql, $params, 'Set registration confirmation number.');
		
		//
		// populate registration associations.
		//
		$groupRegistrations = self::findByRegistrationGroupId($regGroupId);
		$sampleRegistration = reset($groupRegistrations);
		$newRegInfo = $this->getNewRegistrationInfo($sampleRegistration['id']); 

		db_reg_InformationManager::getInstance()->createInformation($regId, $newRegInfo);
		
		db_reg_RegOptionManager::getInstance()->createOptions(array(
			'eventId' => $r['eventId'],
			'regTypeId' => $r['regTypeId'], 
			'regId' => $regId, 
			'optionIds' => $r['regOptionIds']
		));
		
		db_reg_VariableQuantityManager::getInstance()->createOptions($r['regTypeId'], $regId, $r['variableQuantity']);
		
		return $regId;
	}
	
	public function createRegistrations($regs, $payment) {
		$regGroupId = db_reg_GroupManager::getInstance()->createGroup();
			
		// may not have a payment if zero due or event doesn't have any payment types enabled.
		if(!empty($payment)) {
			db_reg_PaymentManager::getInstance()->createPayment($regGroupId, $payment);
		}

		foreach($regs as $r) {
			 $this->createRegistration($regGroupId, $r);
		}
		
		return $regGroupId;
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
				sum(Registration_VariableQuantityOption.quantity) as regOptionCount
			FROM
				Registration
			INNER JOIN
				Registration_VariableQuantityOption
			ON
				Registration.id = Registration_VariableQuantityOption.registrationId
			WHERE
				Registration.dateCancelled is NULL
			AND
				Registration_VariableQuantityOption.variableQuantityId = :variableQuantityId
		';
		
		$params = array(
			'variableQuantityId' => $option['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Find number registered for reg option.');
		
		return $result['regOptionCount'];
	}
	
	public function findByRegistrationGroup($group) {
		return self::findByRegistrationGroupId($group['id']);
	}
	
	public function findByRegistrationGroupId($groupId) {
		$sql = '
			SELECT
				id,
				dateRegistered,
				comments,
				dateCancelled,
				regGroupId,
				categoryId,
				eventId,
				regTypeId,
				confirmationNumber,
				leadNumber
			FROM
				Registration
			WHERE
				regGroupId = :regGroupId
		';
		
		$params = array(
			'regGroupId' => $groupId
		
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
		
		// 3. zero-out var quantity amounts
		foreach($registration['variableQuantity'] as $varQuantity) {
			db_reg_VariableQuantityManager::getInstance()->save(array(
				'id' => $varQuantity['id'],
				'priceId' => $varQuantity['priceId'],
				'quantity' => 0
			));
		}
	}
	
	public function changeRegType($registration, $newRegTypeId) {
		// 1. set new reg type and category.
		$newRegType = db_RegTypeManager::getInstance()->find(array(
			'eventId' => $registration['eventId'],
			'id' => $newRegTypeId
		));
		
		$category = reset($newRegType['visibleTo']); //use first valid category.
		
		$sql = '
			UPDATE
				Registration
			SET
				regTypeId = :regTypeId,
				categoryId = :categoryId
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $registration['id'],
			'regTypeId' => $newRegTypeId,
			'categoryId' => $category['id']
		);
		
		$this->execute($sql, $params, 'Change reg type.');
		
		// 2. remove irrelevant information fields.
		db_reg_InformationManager::getInstance()->retainFieldsByRegType($registration['id'], $newRegTypeId);
		
		// 3. cancel all reg options.
		foreach($registration['regOptions'] as $opt) {
			db_reg_RegOptionManager::getInstance()->cancel($opt['id']);	
		}
		
		// 4. set quantity to 0 for all variable quantity options.
		foreach($registration['variableQuantity'] as $varQuantity) {
			$varQuantity['quantity'] = 0;
			db_reg_VariableQuantityManager::getInstance()->save($varQuantity);
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
	
	public function delete($registration) {
		// delete reg information.
		db_reg_InformationManager::getInstance()->deleteByRegistrationId($registration['id']);
		
		// delete reg options.
		db_reg_RegOptionManager::getInstance()->deleteByRegistrationId($registration['id']);
		
		// delete var quantitiy options.
		db_reg_VariableQuantityManager::getInstance()->deleteByRegistrationId($registration['id']);
		
		$sql = '
			DELETE FROM
				Registration
			WHERE
				id = :id
		';

		$params = array(
			'id' => $registration['id']
		);
		
		$this->execute($sql, $params, 'Delete registration.');
	}
	
	public function deleteByEventId($eventId) {
		$regs = $this->findByEventId($eventId);
		
		foreach($regs as $r) {
			$this->delete($r);
		}
	}
	
	/**
	 * Create a lead number for the given registration--WARNING: this will
	 * commit any existing transaction and begin a new transaction.  
	 * 
	 * @param integer $registrationId
	 */
	public function createLeadNumber($eventId, $registrationId) {
		// explicitly commit the work that has already been done. the 'lock tables'
		// statements below will implicitly commit the current transaction, but we 
		// want PDO to be aware of the current transaction state. 
		$this->commitTransaction();
		
		// lock the Registration table.
		$this->execute('SET autocommit = 0', array(), 'Turn autocommit off.');
		$this->execute('LOCK TABLES Registration WRITE', array(), 'Lock Registration table.');

		do {
			// get a random number in range 5000-99999.
			$leadNumber = $this->rawQueryUnique('SELECT FLOOR(5000 + (RAND()*94999)) as randNumber', array(), 'Get random lead number.');
			$leadNumber = $leadNumber['randNumber'];
			
			// check if it's unique for this event.
			$sql = '
				SELECT 
					COUNT(*) as isUnique
				FROM 
					Registration 
				WHERE 
					eventId = :eventId
				AND
					leadNumber = :leadNumber
			';
			
			$params = array(
				'eventId' => $eventId,
				'leadNumber' => $leadNumber
			);
			
			$isUnique = $this->rawQueryUnique($sql, $params, 'Check if lead number is unique');
			$isUnique = intval($isUnique['isUnique'], 10); 	
		} while($isUnique !== 0);
				
		// update registration with the lead number.
		$sql = '
			UPDATE
				Registration
			SET
				leadNumber = :leadNumber
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $registrationId,
			'leadNumber' => $leadNumber	
		);
		
		$this->execute($sql, $params, 'Set lead number.');

		// unlock the Registration table. this also commits the lead number update.
		$this->execute('COMMIT', array(), 'Commit.');
		$this->execute('UNLOCK TABLES', array(), 'Unlock tables.');
		
		// start a new transaction because we committed the existing one at the 
		// beginning of this method.
		$this->beginTransaction(); 
	}
	
	public function findInfoOrderedByField($eventId, $fieldId, $templateIds, $startDate, $endDate) {
		$params = array(
			'eventId' => $eventId,
			'contactFieldId' => $fieldId,
			'templateIds' => $templateIds
		);
		
		$startDateRestriction = '';
		$endDateRestriction = '';
		if(!empty($startDate)) {
			$startDateRestriction = 'Registration.dateRegistered >= :startDate AND ';
			$params['startDate'] = $startDate;
		}
		if(!empty($endDate)) {
			$endDateRestriction = 'Registration.dateRegistered < :endDate AND ';
			$params['endDate'] = $endDate;
		}
		
		$sql = "
			SELECT
				Registration.id,
				Registration.dateRegistered,
				Registration.dateCancelled,
				Registration.categoryId,
				Registration.eventId,
				Registration.regTypeId
			FROM
				Registration
			INNER JOIN
				Registration_Information
			ON
				Registration.id = Registration_Information.registrationId
			WHERE
				Registration.eventId = :eventId
			AND
				{$startDateRestriction}
				{$endDateRestriction}

				Registration.dateCancelled is NULL
			AND
				Registration_Information.contactFieldId = :contactFieldId
			AND
				Registration.regTypeId IN (
					SELECT 
						BadgeTemplate_RegType.regTypeId 
					FROM
						BadgeTemplate_RegType
					WHERE
						BadgeTemplate_RegType.badgeTemplateId IN (:[templateIds])
				)
			ORDER BY
				Registration_Information.value ASC
		";
		
		return $this->rawQuery($sql, $params, 'Find registration info ordered by field.');
	}
}

?>