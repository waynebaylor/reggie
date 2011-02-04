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
					paymentTypeId,
					regGroupId,
					transactionDate,
					checkNumber,
					amount,
					paymentReceived
				)
			VALUES(
				:paymentTypeId,
				:regGroupId,
				:transactionDate,
				:checkNumber,
				:amount,
				:paymentReceived
			)
		';
		
		$today = new DateTime();
		$received = ArrayUtil::getValue($check, 'paymentReceived', 'false');
		
		$params = array(
			'paymentTypeId' => model_PaymentType::$CHECK,
			'regGroupId' => $groupId,
			'transactionDate' => date_format($today,'Y-m-d H:i'),
			'checkNumber' => $check['checkNumber'],
			'amount' => $received === 'true'? $check['amount'] : 0.00,
			'paymentReceived' => $received
		);
		
		$this->execute($sql, $params, 'Create registration check payment.');
	}
	
	private function createPoPayment($groupId, $po) {
		$sql = '
			INSERT INTO
				Payment(
					paymentTypeId,
					regGroupId,
					transactionDate,
					purchaseOrderNumber,
					amount,
					paymentReceived
				)
			VALUES(
				:paymentTypeId,
				:regGroupId,
				:transactionDate,
				:purchaseOrderNumber,
				:amount,
				:paymentReceived
			)
		';
		
		$today = new DateTime();
		$received = ArrayUtil::getValue($po, 'paymentReceived', 'false');
		
		$params = array(
			'paymentTypeId' => model_PaymentType::$PO,
			'regGroupId' => $groupId,
			'transactionDate' => date_format($today,'Y-m-d H:i'),
			'purchaseOrderNumber' => $po['purchaseOrderNumber'],
			'amount' => $received === 'true'? $po['amount'] : 0.00,
			'paymentReceived' => ArrayUtil::getValue($po, 'paymentReceived', 'false')
		);
		
		$this->execute($sql, $params, 'Create registration PO payment.');
	}
	
	private function createAuthorizeNetPayment($groupId, $authNet) {
		$sql = '
			INSERT INTO
				Payment(
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
			'paymentTypeId' => model_PaymentType::$AUTHORIZE_NET,
			'regGroupId' => $groupId,
			'transactionDate' => date_format($today,'Y-m-d H:i'),
			'paymentReceived' => 'true',
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
		if($paymentInfo['paymentTypeId'] == model_PaymentType::$CHECK) {
			$this->saveCheck($paymentInfo);
		}
		else if($paymentInfo['paymentTypeId'] == model_PaymentType::$PO) {
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
			'amount' => $received === 'true'? $check['amount'] : 0.00,
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
			'amount' => $received === 'true'? $po['amount'] : 0.00,
			'paymentReceived' => $po['paymentReceived']
		);
		
		$this->execute($sql, $params, 'Save purchase order payment.');
	}
}

?>