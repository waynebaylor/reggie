<?php

class db_payment_CheckDirectionsManager extends db_Manager
{
	private static $instance;
	
	function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_payment_CheckDirectionsManager();
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
				CheckDirections.id as id,
				CheckDirections.paymentTypeId as paymentTypeId,
				PaymentType.displayName as displayName,
				CheckDirections.eventId as eventId,
				CheckDirections.instructions as instructions
			FROM
				CheckDirections
			INNER JOIN 
				PaymentType
			ON
				CheckDirections.paymentTypeId = PaymentType.id
			WHERE
				CheckDirections.id = :id
			AND
				CheckDirections.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find check payment directions.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEvent($params) {
		$sql = '
			SELECT
				CheckDirections.id as id,
				CheckDirections.paymentTypeId as paymentTypeId,
				PaymentType.displayName as displayName,
				CheckDirections.eventId as eventId,
				CheckDirections.instructions as instructions
			FROM
				CheckDirections
			INNER JOIN
				PaymentType
			ON
				CheckDirections.paymentTypeId = PaymentType.id
			WHERE
				CheckDirections.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->queryUnique($sql, $params, 'Find event check payment directions.');
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
				CheckDirections(
					eventId,
					instructions
				)
			VALUES(
				:eventId,
				:instructions
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'instructions'));
		
		$this->execute($sql, $params, 'Create check payment directions.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$sql = '
			DELETE FROM
				CheckDirections
			WHERE
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		$this->execute($sql, $params, 'Delete check payment directions.');
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