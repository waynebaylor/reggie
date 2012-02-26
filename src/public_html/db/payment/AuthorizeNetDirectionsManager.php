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
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
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
				AuthorizeNetDirections.id = :id
			AND
				AuthorizeNetDirections.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find Authorize.NET payment directions.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEvent($params) {
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
				AuthorizeNetDirections.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->queryUnique($sql, $params, 'Find Authorize.NET payment directions by event.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, instructions, login, transactionKey]
	 */
	public function create($params) {
		// directions cannot be updated once created. if changes need to be made
		// then a new row must replace the existing row. that's why we delete 
		// any existing rows before creating the new one.
		$this->delete($params);
		
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
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'instructions',
			'login',
			'transactionKey'
		));
		
		$this->execute($sql, $params, 'Create Authorize.NET payment directions.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$sql = '
			DELETE FROM
				AuthorizeNetDirections
			WHERE
				eventId=:eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		$this->execute($sql, $params, 'Delete Authorize.NET payment directions.');
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