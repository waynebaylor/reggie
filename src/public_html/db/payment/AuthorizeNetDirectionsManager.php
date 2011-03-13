<?php
	
class db_payment_AuthorizeNetDirectionsManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_payment_AuthorizeNetDirectionsManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				AuthorizeNetDirections.id as id,
				AuthorizeNetDirections.paymentTypeId as paymentTypeId,
				PaymentType.displayName as displayName,
				AuthorizeNetDirections.eventId as eventId,
				AuthorizeNetDirections.instructions as instructions,
				AuthorizeNetDirections.login as login,
				AuthorizeNetDirections.transactionKey as transactionKey
			FROM
				AuthorizeNetDirections
			INNER JOIN
				PaymentType
			ON
				AuthorizeNetDirections.paymentTypeId = PaymentType.id
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find Authorize.NET payment directions.');
	}
	
	public function findByEvent($event) {
		$sql = '
			SELECT
				AuthorizeNetDirections.id as id,
				AuthorizeNetDirections.paymentTypeId as paymentTypeId,
				PaymentType.displayName as displayName,
				AuthorizeNetDirections.eventId as eventId,
				AuthorizeNetDirections.instructions as instructions,
				AuthorizeNetDirections.login as login,
				AuthorizeNetDirections.transactionKey as transactionKey
			FROM
				AuthorizeNetDirections
			INNER JOIN
				PaymentType
			ON
				AuthorizeNetDirections.paymentTypeId = PaymentType.id
			WHERE
				AuthorizeNetDirections.eventId=:eventId
		';
		
		$params = array(
			'eventId' => $event['id']
		);
		
		return $this->queryUnique($sql, $params, 'Find Authorize.NET payment directions by event.');
	}
	
	public function create($directions) {
		// directions cannot be updated once created. if changes need to be made
		// then a new row must replace the existing row. that's why we delete 
		// any existing rows before creating the new one.
		$this->delete($directions);
		
		$sql = '
			INSERT INTO
				AuthorizeNetDirections(
					eventId,
					instructions,
					login,
					transactionKey
				)
			VALUES(
				:eventId,
				:instructions,
				:login,
				:transactionKey
			)
		';
		
		$params = array(
			'eventId' => $directions['eventId'],
			'instructions' => $directions['instructions'],
			'login' => $directions['login'],
			'transactionKey' => $directions['transactionKey']
		);
		
		$this->execute($sql, $params, 'Create Authorize.NET payment directions.');
	}
	
	public function deleteByEventId($eventId) {
		$sql = '
			DELETE FROM
				AuthorizeNetDirections
			WHERE
				eventId=:eventId
		';
		
		$params = array(
			'eventId' => $eventId
		);
		
		$this->execute($sql, $params, 'Delete Authorize.NET payment directions.');
	}
	
	public function delete($directions) {
		$this->deleteByEventId($directions['eventId']);
	}
}

?>