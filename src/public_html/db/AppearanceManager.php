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
				headerBackgroundColor,
				footerBackgroundColor,
				menuTitle,
				menuBackgroundColor,
				backgroundColor,
				formBackgroundColor,
				buttonTextColor,
				buttonBackgroundColor,
				pageBackgroundColor,
				menuTitleBackgroundColor,
				menuHighlightColor
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
				headerBackgroundColor,
				footerBackgroundColor,
				menuTitle,
				menuBackgroundColor,
				backgroundColor,
				formBackgroundColor,
				buttonTextColor,
				buttonBackgroundColor,
				pageBackgroundColor,
				menuTitleBackgroundColor,
				menuHighlightColor
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
	
	public function createAppearance($eventId, $displayName) {
		$sql = '
			INSERT INTO
				Appearance(
					eventId,
					headerContent,
					footerContent,
					headerBackgroundColor,
					backgroundColor,
					footerBackgroundColor,
					menuTitle,
					menuBackgroundColor,
					formBackgroundColor,
					buttonTextColor,
					buttonBackgroundColor,
					pageBackgroundColor,
					menuTitleBackgroundColor,
					menuHighlightColor
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
				"26211e",
				"ffffff",
				"88bbcc",
				"ffff00"
			)
		';

		$params = array(
			'eventId' => $eventId,
			'headerContent' => '<div style="text-align:center; font-weight:bold; font-size:2.5em;">'.$displayName.'</div>',
			'menuTitle' => 'Registration Menu'
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
				headerBackgroundColor = :headerBackgroundColor,
				footerBackgroundColor = :footerBackgroundColor,
				menuTitle = :menuTitle,
				menuBackgroundColor = :menuBackgroundColor,
				backgroundColor = :backgroundColor,
				formBackgroundColor = :formBackgroundColor,
				buttonTextColor = :buttonTextColor,
				buttonBackgroundColor = :buttonBackgroundColor,
				pageBackgroundColor = :pageBackgroundColor,
				menuTitleBackgroundColor = :menuTitleBackgroundColor,
				menuHighlightColor = :menuHighlightColor
			WHERE
				id = :id
		';
		
		$params = RequestUtil::getParameters(array(
			'id',
			'headerContent',
			'footerContent',
			'headerBackgroundColor',
			'footerBackgroundColor',
			'menuTitle',
			'menuBackgroundColor',
			'backgroundColor',
			'formBackgroundColor',
			'buttonTextColor',
			'buttonBackgroundColor',
			'pageBackgroundColor',
			'menuTitleBackgroundColor',
			'menuHighlightColor'
		));
		
		$this->execute($sql, $params, 'Save event appearance.');
	}
	
	public function deleteByEventId($eventId) {
		$sql = '
			DELETE FROM
				Appearance
			WHERE
				eventId = :eventId
		';
		
		$params = array();
		
		$this->execute($sql, $params, 'Delete event appearance.');
	}
}

?>