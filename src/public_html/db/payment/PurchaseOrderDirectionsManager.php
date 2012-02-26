<?php

class db_payment_PurchaseOrderDirectionsManager extends db_Manager
{
	private static $instance;
	
	function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_payment_PurchaseOrderDirectionsManager();
		}
		
		return self::$instance;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
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
				PurchaseOrderDirections.id = :id
			AND
				PurchaseOrderDirections.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find purchase order payment directions.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEvent($params) {
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
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->queryUnique($sql, $params, 'Find event purchase order payment directions.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, instructions]
	 */
	public function create($params) {
		// directions cannot be updated once created. if changes need to be made
		// then a new row must replace the existing row. that's why we delete 
		// any existing rows before creating the new one.
		$this->delete($params);
		
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
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'instructions'));
		
		$this->execute($sql, $params, 'Create purchase order payment directions.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) { 
		$sql = '
			DELETE FROM
				PurchaseOrderDirections
			WHERE
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		$this->execute($sql, $params, 'Delete purchase order payment directions.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function delete($params) { 
		$this->deleteByEventId($params);
	}
}

?>