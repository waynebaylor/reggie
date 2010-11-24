<?php

class db_EventManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Event';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		// remove the "seconds" from the end of the datetime.
		$obj['regOpen'] = substr($obj['regOpen'], 0, -3);
		$obj['regClosed'] = substr($obj['regClosed'], 0, -3);
	
		$obj['pages'] = db_PageManager::getInstance()->findByEvent($obj);
		$obj['regTypes'] = db_RegTypeManager::getInstance()->findByEvent($obj);
		
		$obj['appearance'] = db_AppearanceManager::getInstance()->findByEvent($obj);
		$obj['emailTemplate'] = db_EmailTemplateManager::getInstance()->findByEvent($obj);
		$obj['reports'] = db_ReportManager::getInstance()->findByEvent($obj);
		
		$obj['paymentTypes'] = db_payment_PaymentTypeManager::getInstance()->findByEvent($obj);
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_EventManager();
		}
		
		return self::$instance;
	}
	
	public function getUserActive($user) {
		$sql = '
			SELECT
				Event.id,
				Event.code,
				Event.displayName,
				Event.regOpen,
				Event.regClosed,
				Event.capacity,
				Event.cancellationPolicy,
				Event.regClosedText
			FROM
				Event
			INNER JOIN
				User_Event
			ON
				User_Event.eventId = Event.id
			WHERE
				User_Event.userId = :userId
			AND
				regOpen<=:openDate
			AND
				regClosed>:closedDate
		';

		$params = array(
			'userId' => $user['id'],
			'openDate' => date(db_Manager::$DATE_FORMAT),
			'closedDate' => date(db_Manager::$DATE_FORMAT)
		);
		
		return $this->query($sql, $params, 'Find active user events.');
	}
	
	public function getUserUpcoming($user) {
		$sql = '
			SELECT
				Event.id,
				Event.code,
				Event.displayName,
				Event.regOpen,
				Event.regClosed,
				Event.capacity,
				Event.cancellationPolicy,
				Event.regClosedText
			FROM
				Event
			INNER JOIN
				User_Event
			ON
				User_Event.eventId = Event.id
			WHERE
				User_Event.userId = :userId
			AND
				regOpen>:openDate
		';

		$params = array(
			'userId' => $user['id'],
			'openDate' => date(db_Manager::$DATE_FORMAT)
		);
		
		return $this->query($sql, $params, 'Find upcoming user events.');
	}
	
	public function getUserInactive($user) {
		$sql = '
			SELECT
				Event.id,
				Event.code,
				Event.displayName,
				Event.regOpen,
				Event.regClosed,
				Event.capacity,
				Event.cancellationPolicy,
				Event.regClosedText
			FROM
				Event
			INNER JOIN
				User_Event
			ON
				User_Event.eventId = Event.id
			WHERE
				User_Event.userId = :userId
			AND
				regClosed<=:closedDate
		';

		$params = array(
			'userId' => $user['id'],
			'closedDate' => date(db_Manager::$DATE_FORMAT)
		);
		
		return $this->query($sql, $params, 'Find active user events.');
	}
	
	public function getAllActive() {
		$sql = '
			SELECT
				id,
				code,
				displayName,
				regOpen,
				regClosed,
				capacity,
				cancellationPolicy,
				regClosedText
			FROM
				Event
			WHERE
				regOpen<=:openDate
			AND
				regClosed>:closedDate
		';

		$params = array(
			'openDate' => date(db_Manager::$DATE_FORMAT),
			'closedDate' => date(db_Manager::$DATE_FORMAT)
		);
		
		return $this->query($sql, $params, 'Find active events.');
	}
	
	public function getAllUpcoming() {
		$sql = '
			SELECT
				id,
				code,
				displayName,
				regOpen,
				regClosed,
				capacity,
				cancellationPolicy,
				regClosedText
			FROM
				Event
			WHERE
				regOpen>:openDate
		';

		$params = array(
			'openDate' => date(db_Manager::$DATE_FORMAT)
		);
		
		return $this->query($sql, $params, 'Find upcoming events.');
	}
	
	public function getAllInactive() {
		$sql = '
			SELECT
				id,
				code,
				displayName,
				regOpen,
				regClosed,
				capacity,
				cancellationPolicy,
				regClosedText
			FROM
				Event
			WHERE
				regClosed<=:closedDate
		';

		$params = array(
			'closedDate' => date(db_Manager::$DATE_FORMAT)
		);
		
		return $this->query($sql, $params, 'Find active events.');
	}
	
	public function createEvent($event) {
		$sql = '
			INSERT INTO
				Event(
					code,
					displayName,
					regOpen,
					regClosed,
					capacity,
					cancellationPolicy,
					regClosedText
				)
			VALUES(
				:code,
				:displayName,
				:regOpen,
				:regClosed,
				:capacity,
				:cancellationPolicy,
				:regClosedText
			)
		';

		$params = array(
			'code' => $event['code'],
			'displayName' => $event['displayName'],
			'regOpen' => $event['regOpen'],
			'regClosed' => $event['regClosed'],
			'capacity' => 0,
			'cancellationPolicy' => '',
			'regClosedText' => ''
		);
		
		$this->execute($sql, $params, 'Create event.');
		
		$id = $this->lastInsertId();
		
		// create the event's appearance information.
		db_AppearanceManager::getInstance()->createAppearance($id);
		 
		//create the event's email template.
		db_EmailTemplateManager::getInstance()->createEmailTemplate($id);
		
		return $id;
	}
	
	public function save($event) {
		$sql = '
			UPDATE
				Event
			SET
				code=:code,
				displayName=:displayName,
				regOpen=:regOpen,
				regClosed=:regClosed,
				capacity=:capacity,
				cancellationPolicy=:cancellationPolicy,
				regClosedText=:regClosedText
			WHERE
				id=:id
		';
		
		$params = array(
			'code' => $event['code'],
			'displayName' => $event['displayName'],
			'regOpen' => $event['regOpen'],
			'regClosed' => $event['regClosed'],
			'capacity' => $event['capacity'],
			'cancellationPolicy' => $event['cancellationPolicy'],
			'regClosedText' => $event['regClosedText'],
			'id' => $event['id']
		);
		
		$this->execute($sql, $params, 'Save event.');
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				code,
				displayName,
				regOpen,
				regClosed,
				capacity,
				cancellationPolicy,
				regClosedText
			FROM
				Event
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find event by id.');
	}
	
	public function findByCode($code) {
		$sql = '
			SELECT
				id,
				code,
				displayName,
				regOpen,
				regClosed,
				capacity,
				cancellationPolicy,
				regClosedText
			FROM
				Event
			WHERE
				code=:code
		';
		
		$params = array(
			'code' => $code
		);
		
		return $this->queryUnique($sql, $params, 'Find event by code.');
	}
}

?>