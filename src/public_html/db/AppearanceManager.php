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
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
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
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find event appearance.');
	}
	
	/**
	 * 
	 * @param array $params [id]
	 */
	public function findByEvent($params) {
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
			'eventId' => $params['id']
		);
		
		return $this->queryUnique($sql, $params, 'Find appearance by event.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, displayName]
	 */
	public function createAppearance($params) {
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
			'eventId' => $params['eventId'],
			'headerContent' => '<div style="text-align:center; font-weight:bold; font-size:2.5em;">'.$params['displayName'].'</div>',
			'menuTitle' => 'Registration Menu'
		);
		
		$this->execute($sql, $params, 'Create event appearance.');
	}
	
	public function save($params) {
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
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'id',
			'eventId',
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
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$sql = '
			DELETE FROM
				Appearance
			WHERE
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		$this->execute($sql, $params, 'Delete event appearance.');
	}
}

?>