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
				:cancellationPolicy,
				:regClosedText,
				:paymentInstructions
			)
		';

		$params = array(
			'code' => $event['code'],
			'displayName' => $event['displayName'],
			'regOpen' => $event['regOpen'],
			'regClosed' => $event['regClosed'],
			'capacity' => 0,
			'cancellationPolicy' => '',
			'regClosedText' => '',
			'paymentInstructions' => ''
		);
		
		$this->execute($sql, $params, 'Create event.');
		
		$id = $this->lastInsertId();
		
		// create the event's appearance information.
		db_AppearanceManager::getInstance()->createAppearance($id, $event['displayName']);
		 
		// create the event's group registration information.
		db_GroupRegistrationManager::getInstance()->createGroupRegistration($id);
		
		// create built-in reports.
		db_ReportManager::getInstance()->createPaymentsToDate($id);
		db_ReportManager::getInstance()->createAllRegToDate($id);
		db_ReportManager::getInstance()->createOptionCount($id);
		db_ReportManager::getInstance()->createRegTypeBreakdown($id);
		
		// create event pages.
		$visibleToCategoryIds = array(1); // attendee only.
		$this->createRegTypeTemplatePage($id, $visibleToCategoryIds);
		$this->createContactInfoTemplatePage($id, $visibleToCategoryIds);
		$this->createConferenceRegTemplatePage($id, $visibleToCategoryIds);
		$this->createSpecialEventsTemplatePage($id, $visibleToCategoryIds);
		$this->createSurveyTemplatePage($id, $visibleToCategoryIds);
		
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
				cancellationPolicy = :cancellationPolicy,
				regClosedText = :regClosedText,
				paymentInstructions = :paymentInstructions
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
	
	private function createRegTypeTemplatePage($eventId, $categoryIds) {
		$pageId = db_PageManager::getInstance()->createPage($eventId, 'Registration Type', $categoryIds);
		$textSectionId = db_PageSectionManager::getInstance()->createSection($eventId, $pageId, 'reg type text', model_ContentType::$TEXT);
		db_PageSectionManager::getInstance()->save(array(
			'id' => $textSectionId,
			'name' => 'reg type text',
			'text' => 'Plese choose a registration type below.',
			'numbered' => 'F'
		));
		
		$regTypeSectionId = db_PageSectionManager::getInstance()->createSection($eventId, $pageId, 'reg types', model_ContentType::$REG_TYPE);
		db_RegTypeManager::getInstance()->createRegType($eventId, $regTypeSectionId, 'Member', 'M', $categoryIds);
		db_RegTypeManager::getInstance()->createRegType($eventId, $regTypeSectionId, 'Non-Member', 'NM', $categoryIds);
	}

	private function createContactInfoTemplatePage($eventId, $categoryIds) {
		db_PageManager::getInstance()->createPage($eventId, 'Contact Information', $categoryIds);
	}

	private function createConferenceRegTemplatePage($eventId, $categoryIds) {
		db_PageManager::getInstance()->createPage($eventId, 'Conference Registration', $categoryIds);
		
	}

	private function createSpecialEventsTemplatePage($eventId, $categoryIds) {
		db_PageManager::getInstance()->createPage($eventId, 'Special Events', $categoryIds);

	}

	private function createSurveyTemplatePage($eventId, $categoryIds) {
		db_PageManager::getInstance()->createPage($eventId, 'Survey', $categoryIds);

	}
}

?>