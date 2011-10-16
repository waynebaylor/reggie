<?php

class db_EventManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		// remove the "seconds" from the end of the datetime.
		$obj['regOpen'] = substr($obj['regOpen'], 0, -3);
		$obj['regClosed'] = substr($obj['regClosed'], 0, -3);
	
		$obj['pages'] = db_PageManager::getInstance()->findByEvent($obj);
		$obj['regTypes'] = db_RegTypeManager::getInstance()->findByEvent($obj);
		
		$obj['appearance'] = db_AppearanceManager::getInstance()->findByEvent($obj);
		$obj['emailTemplates'] = db_EmailTemplateManager::getInstance()->findByEvent($obj);
		$obj['groupRegistration'] = db_GroupRegistrationManager::getInstance()->findByEvent($obj);
		
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
				Event.confirmationText,
				Event.cancellationPolicy,
				Event.regClosedText,
				Event.paymentInstructions
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
				Event.confirmationText,
				Event.cancellationPolicy,
				Event.regClosedText,
				Event.paymentInstructions
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
				Event.confirmationText,
				Event.cancellationPolicy,
				Event.regClosedText,
				Event.paymentInstructions
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
				confirmationText,
				cancellationPolicy,
				regClosedText,
				Event.paymentInstructions
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
				confirmationText,
				cancellationPolicy,
				regClosedText,
				Event.paymentInstructions
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
				confirmationText,
				cancellationPolicy,
				regClosedText,
				Event.paymentInstructions
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
					confirmationText,
					cancellationPolicy,
					regClosedText,
					paymentInstructions
				)
			VALUES(
				:code,
				:displayName,
				:regOpen,
				:regClosed,
				:capacity,
				:confirmationText,
				:cancellationPolicy,
				:regClosedText,
				:paymentInstructions
			)
		';

		unset($event['id']);
		$params = $event;
		
		$this->execute($sql, $params, 'Create event.');
		
		$id = $this->lastInsertId();
		
		// create the event's appearance information.
		db_AppearanceManager::getInstance()->createAppearance($id, $event['displayName']);
		 
		// create the event's group registration information.
		db_GroupRegistrationManager::getInstance()->createGroupRegistration($id);
		
		// create defaults based on template.
		db_EventTemplate::getInstance()->createDefaults($id);
		
		return $id;
	}
	
	public function save($event) {
		$sql = '
			UPDATE
				Event
			SET
				code = :code,
				displayName = :displayName,
				regOpen = :regOpen,
				regClosed = :regClosed,
				capacity = :capacity,
				confirmationText = :confirmationText,
				cancellationPolicy = :cancellationPolicy,
				regClosedText = :regClosedText,
				paymentInstructions = :paymentInstructions
			WHERE
				id = :id
		';
		
		$params = array(
			'code' => $event['code'],
			'displayName' => $event['displayName'],
			'regOpen' => $event['regOpen'],
			'regClosed' => $event['regClosed'],
			'capacity' => $event['capacity'],
			'confirmationText' => $event['confirmationText'],
			'cancellationPolicy' => $event['cancellationPolicy'],
			'regClosedText' => $event['regClosedText'],
			'id' => $event['id'],
			'paymentInstructions' => $event['paymentInstructions']
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
				confirmationText,
				cancellationPolicy,
				regClosedText,
				paymentInstructions
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
				confirmationText,
				cancellationPolicy,
				regClosedText,
				paymentInstructions
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
	
	public function delete($eventId) {   
		/////////////////////////////////////////////////////////////////////////////////
		// delete event payments.
		db_reg_PaymentManager::getInstance()->deleteByEventId($eventId);
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete event registrations.
		db_reg_RegistrationManager::getInstance()->deleteByEventId($eventId);
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete reports.
		db_ReportManager::getInstance()->deleteByEventId($eventId);
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete badge templates
		db_BadgeTemplateManager::getInstance()->deleteByEventId($eventId);
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete user associations.
		$sql = '
			DELETE FROM
				User_Role
			WHERE
				eventId = :eventId
		';
		
		$params = array(
			'eventId' => $eventId
		);
		
		$this->execute($sql, $params, 'Delete user associations.');
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete appearance, email templates, payment options, group registration.
		db_AppearanceManager::getInstance()->deleteByEventId($eventId);    
		db_EmailTemplateManager::getInstance()->deleteByEventId($eventId);
		db_payment_CheckDirectionsManager::getInstance()->deleteByEventId($eventId);   
		db_payment_PurchaseOrderDirectionsManager::getInstance()->deleteByEventId($eventId);
		db_payment_AuthorizeNetDirectionsManager::getInstance()->deleteByEventId($eventId);
		db_GroupRegistrationManager::getInstance()->deleteByEventId($eventId);
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete event pages.
		db_PageManager::getInstance()->deleteByEventId($eventId);
		
		///////////////////////////////////////////////////////////////////////////////
		// delete static pages.
		db_StaticPageManager::getInstance()->deleteByEventId($eventId);
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete event.
		$sql = '
			DELETE FROM
				Event
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $eventId
		);
		
		$this->execute($sql, $params, 'Delete event.');
	}
	
	public function findInfoById($id) {
		$sql = '
			SELECT
				id,
				code,
				displayName,
				regOpen,
				regClosed,
				capacity,
				confirmationText,
				cancellationPolicy,
				regClosedText,
				paymentInstructions
			FROM
				Event
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->rawQueryUnique($sql, $params, 'Find event info by id.');
	}
	
	public function findAllInfo() {
		$sql = '
			SELECT
				id,
				code,
				displayName,
				regOpen,
				regClosed,
				capacity,
				confirmationText,
				cancellationPolicy,
				regClosedText,
				paymentInstructions
			FROM
				Event
			ORDER BY
				displayName
		';
		
		return $this->rawQuery($sql, array(), 'Find all event infos.');
	}
	
	public function findInfoByUserId($userId) {
		$user = db_UserManager::getInstance()->find($userId);
		if(model_Role::userHasRole($user, array(model_Role::$SYSTEM_ADMIN, model_Role::$EVENT_ADMIN))) {
			return $this->findAllInfo();
		}
		else {
			$sql = '
				SELECT DISTINCT
					Event.id,
					Event.code,
					Event.displayName,
					Event.regOpen,
					Event.regClosed,
					Event.capacity,
					Event.confirmationText,
					Event.cancellationPolicy,
					Event.regClosedText,
					Event.paymentInstructions
				FROM
					Event
				INNER JOIN
					User_Role
				ON
					Event.id = User_Role.eventId
				WHERE
					User_Role.userId = :userId
				ORDER BY
					Event.displayName
			';
			
			$params = array(
				'userId' => $userId
			);
			
			return $this->rawQuery($sql, $params, 'Find event info accessible to user.');
		}
	}
}

?>