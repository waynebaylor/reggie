<?php

class db_AppearanceManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
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
				menuTitle,
				menuColor,
				backgroundColor,
				formColor,
				buttonTextColor,
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
				menuTitle,
				menuColor,
				backgroundColor,
				formColor,
				buttonTextColor,
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
					backgroundColor,
					footerColor,
					menuTitle,
					menuColor,
					formColor,
					buttonTextColor,
					buttonColor
				)
			VALUES(
				:eventId,
				:headerContent,
				"",
				"e5e19c",
				"414c4c",
				"e5e19c",
				:menuTitle,
				"ffffff",
				"ffffff",
				"ffffff",
				"26211e"
			)
		';

		$params = array(
			'eventId' => $eventId,
			'headerContent' => '<span style="font-size:2em;">New Event</span>',
			'menuTitle' => '<div style="background-color:#88bbcc; border-bottom:1px solid black; padding:5px;">Registration Menu</div>'
		);
		
		$this->execute($sql, $params, 'Create event appearance.');
	}
	
	public function save($appearance) {
		$sql = '
			UPDATE
				Appearance
			SET
				headerContent = :headerContent,
				footerContent = :footerContent,
				headerColor = :headerColor,
				footerColor = :footerColor,
				menuTitle = :menuTitle,
				menuColor = :menuColor,
				backgroundColor = :backgroundColor,
				formColor = :formColor,
				buttonTextColor = :buttonTextColor,
				buttonColor = :buttonColor
			WHERE
				id = :id
		';
		
		$params = RequestUtil::getParameters(array(
			'id',
			'headerContent',
			'footerContent',
			'headerColor',
			'footerColor',
			'menuTitle',
			'menuColor',
			'backgroundColor',
			'formColor',
			'buttonTextColor',
			'buttonColor'
		));
		
		$this->execute($sql, $params, 'Save event appearance.');
	}
}

?>