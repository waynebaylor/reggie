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
			DESC
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
					amount
				)
			VALUES(
				:paymentTypeId,
				:regGroupId,
				:transactionDate,
				:checkNumber,
				:amount
			)
		';
		
		$today = new DateTime();
		
		$params = array(
			'paymentTypeId' => model_PaymentType::$CHECK,
			'regGroupId' => $groupId,
			'transactionDate' => date_format($today,'Y-m-d H:i'),
			'checkNumber' => $check['checkNumber'],
			'amount' => $check['amount']
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
					amount
				)
			VALUES(
				:paymentTypeId,
				:regGroupId,
				:transactionDate,
				:purchaseOrderNumber,
				:amount
			)
		';
		
		$today = new DateTime();
		
		$params = array(
			'paymentTypeId' => model_PaymentType::$PO,
			'regGroupId' => $groupId,
			'transactionDate' => date_format($today,'Y-m-d H:i'),
			'purchaseOrderNumber' => $po['purchaseOrderNumber'],
			'amount' => $po['amount']
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
	
	public function savePaymentReceived($groupId, $paymentIds) {
		// set all payments check and PO payment received values to false.
		$sql = '
			UPDATE
				Payment
			SET
				paymentReceived = :paymentReceived
			WHERE
				regGroupId = :groupId
			AND
				paymentTypeId IN (:checkTypeId, :poTypeId)
		';
		
		$params = array(
			'paymentReceived' => 'false',
			'groupId' => $groupId,
			'checkTypeId' => model_PaymentType::$CHECK,
			'poTypeId' => model_PaymentType::$PO
		);
		
		$this->execute($sql, $params, 'Set all group payments to not received.');
		
		// set the given payment's received values to true.
		$sql = '
			UPDATE 
				Payment
			SET
				paymentReceived = :paymentReceived
			WHERE
				regGroupId = :groupId
			AND
				id = :id
		';
		
		foreach($paymentIds as $id) {
			$params = array(
				'groupId' => $groupId,
				'id' => $id,
				'paymentReceived' => 'true'
			);
		
			$this->execute($sql, $params, 'Set payment received.');
		}
	}
}

?>