<?php

class db_reg_PaymentManager extends db_Manager
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
			self::$instance = new db_reg_PaymentManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				eventId,
				paymentTypeId,
				regGroupId,
				transactionDate,
				paymentReceived,
				checkNumber,
				purchaseOrderNumber,
				cardType,
				cardSuffix,
				authorizationCode,
				transactionId,
				name,
				address,
				city,
				state,
				zip,
				country,
				amount
			FROM
				Payment
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find payment.');
	}
	
	public function findByRegistrationGroup($group) {
		$sql = '
			SELECT
				id,
				eventId,
				paymentTypeId,
				regGroupId,
				transactionDate,
				paymentReceived,
				checkNumber,
				purchaseOrderNumber,
				cardType,
				cardSuffix,
				authorizationCode,
				transactionId,
				name,
				address,
				city,
				state,
				zip,
				country,
				amount
			FROM
				Payment
			WHERE
				regGroupId = :regGroupId
			ORDER BY
				transactionDate
			ASC
		';
		
		$params = array(
			'regGroupId' => $group['id']
		);
		
		return $this->query($sql, $params, 'Find payments by registration group.');
	}
	
	public function createPayment($groupId, $info) {
		switch($info['paymentType']) {
			case model_PaymentType::$CHECK:
				$this->createCheckPayment($groupId, $info);
				break;
			case model_PaymentType::$PO:
				$this->createPoPayment($groupId, $info);
				break;
			case model_PaymentType::$AUTHORIZE_NET:
				$this->createAuthorizeNetPayment($groupId, $info);
				break;
			default:
				throw new Exception('Unknown payment type: '.$info['paymentType']);
		}
	}
	
	private function createCheckPayment($groupId, $check) {
		$sql = '
			INSERT INTO
				Payment(
					eventId,
					paymentTypeId,
					regGroupId,
					transactionDate,
					checkNumber,
					amount,
					paymentReceived
				)
			VALUES(
				:eventId,
				:paymentTypeId,
				:regGroupId,
				:transactionDate,
				:checkNumber,
				:amount,
				:paymentReceived
			)
		';
		
		$today = new DateTime();
		$received = ArrayUtil::getValue($check, 'paymentReceived', 'F');
		
		$params = array(
			'eventId' => $check['eventId'],
			'paymentTypeId' => model_PaymentType::$CHECK,
			'regGroupId' => $groupId,
			'transactionDate' => date_format($today,'Y-m-d H:i'),
			'checkNumber' => $check['checkNumber'],
			'amount' => $received === 'T'? $check['amount'] : 0.00,
			'paymentReceived' => $received
		);
		
		$this->execute($sql, $params, 'Create registration check payment.');
	}
	
	private function createPoPayment($groupId, $po) {
		$sql = '
			INSERT INTO
				Payment(
					eventId,
					paymentTypeId,
					regGroupId,
					transactionDate,
					purchaseOrderNumber,
					amount,
					paymentReceived
				)
			VALUES(
				:eventId,
				:paymentTypeId,
				:regGroupId,
				:transactionDate,
				:purchaseOrderNumber,
				:amount,
				:paymentReceived
			)
		';
		
		$today = new DateTime();
		$received = ArrayUtil::getValue($po, 'paymentReceived', 'F');
		
		$params = array(
			'eventId' => $po['eventId'],
			'paymentTypeId' => model_PaymentType::$PO,
			'regGroupId' => $groupId,
			'transactionDate' => date_format($today,'Y-m-d H:i'),
			'purchaseOrderNumber' => $po['purchaseOrderNumber'],
			'amount' => $received === 'T'? $po['amount'] : 0.00,
			'paymentReceived' => ArrayUtil::getValue($po, 'paymentReceived', 'F')
		);
		
		$this->execute($sql, $params, 'Create registration PO payment.');
	}
	
	private function createAuthorizeNetPayment($groupId, $authNet) {
		$sql = '
			INSERT INTO
				Payment(
					eventId,
					paymentTypeId,
					regGroupId,
					transactionDate,
					paymentReceived,
					cardType,
					cardSuffix,
					authorizationCode,
					transactionId,
					name,
					address,
					city,
					state,
					zip,
					country,
					amount
				)
			VALUES(
				:eventId,
				:paymentTypeId,
				:regGroupId,
				:transactionDate,
				:paymentReceived,
				:cardType,
				:cardSuffix,
				:authorizationCode,
				:transactionId,
				:name,
				:address,
				:city,
				:state,
				:zip,
				:country,
				:amount
			)
		';
		
		$today = new DateTime();
		
		$params = array(
			'eventId' => $authNet['eventId'],
			'paymentTypeId' => model_PaymentType::$AUTHORIZE_NET,
			'regGroupId' => $groupId,
			'transactionDate' => date_format($today,'Y-m-d H:i'),
			'paymentReceived' => 'T',
			'cardType' => $authNet['cardType'],
			'cardSuffix' => $authNet['cardSuffix'],
			'authorizationCode' => $authNet['authorizationCode'],
			'transactionId' => $authNet['transactionId'],
			'name' => $authNet['name'],
			'address' => $authNet['address'],
			'city' => $authNet['city'],
			'state' => $authNet['state'],
			'zip' => $authNet['zip'],
			'country' => $authNet['country'],
			'amount' => $authNet['amount'] 
		);
		
		$this->execute($sql, $params, 'Create registration Authorize.NET payment.');
	}
	
	public function save($paymentInfo) {
		if($paymentInfo['paymentType'] == model_PaymentType::$CHECK) {
			$this->saveCheck($paymentInfo);
		}
		else if($paymentInfo['paymentType'] == model_PaymentType::$PO) {
			$this->savePurchaseOrder($paymentInfo);
		}
	}
	
	public function saveCheck($check) {
		$sql = '
			UPDATE
				Payment
			SET
				checkNumber = :checkNumber,
				amount = :amount,
				paymentReceived = :paymentReceived
			WHERE
				id = :id
		';
		
		$received = $check['paymentReceived'];
		$params = array(
			'id' => $check['id'],
			'checkNumber' => $check['checkNumber'],
			'amount' => $received === 'T'? $check['amount'] : 0.00,
			'paymentReceived' => $received
		);
		
		$this->execute($sql, $params, 'Save check payment.');
	}
	
	public function savePurchaseOrder($po) {
		$sql = '
			UPDATE
				Payment
			SET
				purchaseOrderNumber = :purchaseOrderNumber,
				amount = :amount,
				paymentReceived = :paymentReceived
			WHERE
				id = :id
		';
		
		$received = $po['paymentReceived'];
		$params = array(
			'id' => $po['id'],
			'purchaseOrderNumber' => $po['purchaseOrderNumber'],
			'amount' => $received === 'T'? $po['amount'] : 0.00,
			'paymentReceived' => $po['paymentReceived']
		);
		
		$this->execute($sql, $params, 'Save purchase order payment.');
	}
	
	public function deleteByEventId($eventId) {
		$sql = '
			DELETE FROM
				Payment
			WHERE
				eventId = :eventId
		';
		
		$params = array(
			'eventId' => $eventId
		);
		
		$this->execute($sql, $params, 'Delete event payments.');
	}
	
	public function deletePayment($params) {
		// only allow deleting checks and PO payments.
		$sql = '
			DELETE FROM
				Payment
			WHERE
				eventId = :eventId
			AND
				id = :id
			AND
				paymentTypeId IN (1, 2)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Remove payment.');
	}
	
	public function findAdminPaymentData($regGroupId) {
		$sql = '
			(
			    select 
			        RegOption.id as id,
			        RegOption.code as code,
			        RegOption.description as description,
			        1 as quantity,
			        RegOptionPrice.price as price
			    from
			        RegOption
			    inner join
			        Registration_RegOption 
			    on
			        RegOption.id = Registration_RegOption.regOptionId
			    inner join
			        Registration 
			    on
			        Registration.id = Registration_RegOption.registrationId
			    inner join
			        RegOptionPrice
			    on
			        Registration_RegOption.priceId = RegOptionPrice.id
			    where
			        Registration_RegOption.dateCancelled is null
			    and
			        Registration.dateCancelled is null
			    and
			        RegOptionPrice.price > 0
			    and
			        Registration.regGroupId = :regGroupId
			    and 
			        not exists (select id from Payment where regGroupId = :regGroupId)
			)
			union
			(
			    select 
			        VariableQuantityOption.id as id,
			        VariableQuantityOption.code as code,
			        VariableQuantityOption.description as description,
			        Registration_VariableQuantityOption.quantity as quantity,
			        RegOptionPrice.price as price
			    from
			        VariableQuantityOption
			    inner join
			        Registration_VariableQuantityOption 
			    on
			        VariableQuantityOption.id = Registration_VariableQuantityOption.variableQuantityId
			    inner join
			        Registration 
			    on
			        Registration.id = Registration_VariableQuantityOption.registrationId
			    inner join
			        RegOptionPrice
			    on
			        Registration_VariableQuantityOption.priceId = RegOptionPrice.id
			    where
			        Registration_VariableQuantityOption.quantity > 0
			    and
			        Registration.dateCancelled is null
			    and
			        RegOptionPrice.price > 0
			    and
			        Registration.regGroupId = :regGroupId
			    and 
			        not exists (select id from Payment where regGroupId = :regGroupId)
			)
		';
		
		return $this->rawQuery($sql, array('regGroupId' => $regGroupId), 'finding data for admin payment.');
	}
}

?>