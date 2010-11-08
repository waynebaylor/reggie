<?php

class db_payment_PurchaseOrderDirectionsManager extends db_Manager
{
	private static $instance;
	
	function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'PurchaseOrderDirections';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_payment_PurchaseOrderDirectionsManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				PurchaseOrderDirections.id as id,
				PurchaseOrderDirections.paymentTypeId as paymentTypeId,
				PaymentType.displayName as displayName,
				PurchaseOrderDirections.eventId as eventId,
				PurchaseOrderDirections.instructions as instructions
			FROM
				PurchaseOrderDirections
			INNER JOIN
				PaymentType
			ON
				PurchaseOrderDirections.paymentTypeId = PaymentType.id
			WHERE
				PurchaseOrderDirections.id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find purchase order payment directions.');
	}
	
	public function findByEvent($event) {
		$sql = '
			SELECT
				PurchaseOrderDirections.id as id,
				PurchaseOrderDirections.paymentTypeId as paymentTypeId,
				PaymentType.displayName as displayName,
				PurchaseOrderDirections.eventId as eventId,
				PurchaseOrderDirections.instructions as instructions
			FROM
				PurchaseOrderDirections
			INNER JOIN
				PaymentType
			ON
				PurchaseOrderDirections.paymentTypeId = PaymentType.id	
			WHERE
				eventId=:eventId
		';
		
		$params = array(
			'eventId' => $event['id']
		);
		
		return $this->queryUnique($sql, $params, 'Find event purchase order payment directions.');
	}
	
	public function create($directions) {
		// directions cannot be updated once created. if changes need to be made
		// then a new row must replace the existing row. that's why we delete 
		// any existing rows before creating the new one.
		$this->delete($directions);
		
		$sql = '
			INSERT INTO
				PurchaseOrderDirections(
					eventId,
					instructions
				)
			VALUES(
				:eventId,
				:instructions
			)
		';
		
		$params = array(
			'eventId' => $directions['eventId'],
			'instructions' => $directions['instructions']
		);
		
		$this->execute($sql, $params, 'Create purchase order payment directions.');
	}
	
	public function delete($directions) { 
		$sql = '
			DELETE FROM
				PurchaseOrderDirections
			WHERE
				eventId=:eventId
		';
		
		$params = array(
			'eventId' => $directions['eventId']
		);
		
		$this->execute($sql, $params, 'Delete purchase order payment directions.');
	}
}

?>