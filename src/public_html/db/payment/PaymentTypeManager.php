<?php

class db_payment_PaymentTypeManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_payment_PaymentTypeManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				displayName
			FROM
				PaymentType
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find payment type.');
	}
	
	public function findAll() {
		$sql = '
			SELECT
				id,
				displayName
			FROM
				PaymentType
		';
		
		return $this->query($sql, array(), 'Find all payment types.');
	}
	
	public function findByEvent($event) {
		$check = db_payment_CheckDirectionsManager::getInstance()->findByEvent(array('eventId' => $event['id']));
		$po = db_payment_PurchaseOrderDirectionsManager::getInstance()->findByEvent(array('eventId' => $event['id']));
		$authNet = db_payment_AuthorizeNetDirectionsManager::getInstance()->findByEvent(array('eventId' => $event['id']));
		
		$types = array();
		
		if(!empty($check)) {
			$types[model_PaymentType::$CHECK] = $check;				
		}
		if(!empty($po)) {
			$types[model_PaymentType::$PO] = $po;
		}
		if(!empty($authNet)) {
			$types[model_PaymentType::$AUTHORIZE_NET] = $authNet;
		}

		return $types;
	}
}

?>