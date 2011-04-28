<?php

class db_StaticPageManager extends db_Manager 
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_StaticPageManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				StaticPage.id,
				StaticPage.eventId,
				Event.code as eventCode,
				StaticPage.name,
				StaticPage.title,
				StaticPage.content
			FROM
				StaticPage
			INNER JOIN
				Event
			ON
				StaticPage.eventId = Event.id
			WHERE
				StaticPage.id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find static page.');
	}
	
	public function findByEventId($eventId) {
		$sql = '
			SELECT
				StaticPage.id,
				StaticPage.eventId,
				Event.code as eventCode,
				StaticPage.name,
				StaticPage.title,
				StaticPage.content
			FROM
				StaticPage
			INNER JOIN
				Event
			ON
				StaticPage.eventId = Event.id
			WHERE
				StaticPage.eventId = :eventId
			ORDER BY
				StaticPage.name
		';
		
		$params = array(
			'eventId' => $eventId
		);
		
		return $this->query($sql, $params, 'Find static pages by event.');
	}
	
	public function createPage($params) {
		$sql = '
			INSERT INTO
				StaticPage(
					eventId,
					name,
					title
				)
			VALUES(
				:eventId,
				:name,
				:title
			)
		';
		
		$this->execute($sql, $params, 'Create static event page.');
	}
	
	public function deletePage($id) {
		$sql = '
			DELETE FROM
				StaticPage
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		$this->execute($sql, $params, 'Delete static event page.');
	}
	
	public function save($params) {
		$sql = '
			UPDATE
				StaticPage
			SET
				name = :name,
				title = :title,
				content = :content
			WHERE
				id = :id
		';
		
		$this->execute($sql, $params, 'Save static event page.');
	}
}