<?php

class db_AppearanceManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Appearance';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_AppearanceManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				eventId,
				headerContent,
				footerContent,
				headerColor,
				footerColor,
				menuColor,
				backgroundColor,
				formColor,
				buttonColor
			FROM
				Appearance
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find event appearance.');
	}
	
	public function findByEvent($event) {
		$sql = '
			SELECT
				id,
				eventId,
				headerContent,
				footerContent,
				headerColor,
				footerColor,
				menuColor,
				backgroundColor,
				formColor,
				buttonColor
			FROM
				Appearance
			WHERE
				eventId=:eventId
		';
		
		$params = array(
			'eventId' => $event['id']
		);
		
		return $this->queryUnique($sql, $params, 'Find appearance by event.');
	}
	
	public function createAppearance($eventId) {
		$sql = '
			INSERT INTO
				Appearance(
					eventId,
					headerContent,
					footerContent,
					headerColor,
					footerColor,
					menuColor,
					backgroundColor,
					formColor,
					buttonColor
				)
			VALUES(
				:eventId,
				"<h1>New Event</h1>",
				"New Event",
				"ffffff",
				"ffffff",
				"ffffff",
				"ffffff",
				"ffffff",
				"ffffff"
			)
		';

		$params = array(
			'eventId' => $eventId
		);
		
		$this->execute($sql, $params, 'Create event appearance.');
	}
	
	public function save($appearance) {
		$sql = '
			UPDATE
				Appearance
			SET
				headerContent=:headerContent,
				footerContent=:footerContent,
				headerColor=:headerColor,
				footerColor=:footerColor,
				menuColor=:menuColor,
				backgroundColor=:backgroundColor,
				formColor=:formColor,
				buttonColor=:buttonColor
			WHERE
				id=:id
		';
		
		$params = RequestUtil::getParameters(array(
			'id',
			'headerContent',
			'footerContent',
			'headerColor',
			'footerColor',
			'menuColor',
			'backgroundColor',
			'formColor',
			'buttonColor'
		));
		
		$this->execute($sql, $params, 'Save event appearance.');
	}
}

?>