<?php

class db_payment_CheckDirectionsManager extends db_Manager
{
	private static $instance;
	
	function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'CheckDirections';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_payment_CheckDirectionsManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
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
				CheckDirections.id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find check payment directions.');
	}
	
	public function findByEvent($event) {
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
				CheckDirections.eventId=:eventId
		';
		
		$params = array(
			'eventId' => $event['id']
		);
		
		return $this->queryUnique($sql, $params, 'Find event check payment directions.');
	}
	
	public function create($directions) {
		// directions cannot be updated once created. if changes need to be made
		// then a new row must replace the existing row. that's why we delete 
		// any existing rows before creating the new one.
		$this->delete($directions);
		
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
		
		$params = array(
			'eventId' => $directions['eventId'],
			'instructions' => $directions['instructions']
		);
		
		$this->execute($sql, $params, 'Create check payment directions.');
	}
	
	public function delete($directions) {
		$sql = '
			DELETE FROM
				CheckDirections
			WHERE
				eventId=:eventId
		';
		
		$params = array(
			'eventId' => $directions['eventId']
		);
		
		$this->execute($sql, $params, 'Delete check payment directions.');
	}
}

?>