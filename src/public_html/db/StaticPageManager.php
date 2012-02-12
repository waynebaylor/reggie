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
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
		return $this->findByIdAndEvent(array(
			'eventId' => $params['eventId'],
			'pageId' => $params['id']
		));
	}
	
	/**
	 * 
	 * @param array $params [eventCode, name]
	 */
	public function findByEventCodeAndName($params) {
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
				Event.code = :eventCode
			AND
				StaticPage.name = :name
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventCode', 'name'));
		
		return $this->queryUnique($sql, $params, 'Find static page by event code and name.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEventId($params) {
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
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->query($sql, $params, 'Find static pages by event.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, name, title, content]
	 */
	public function createPage($params) {
		$sql = '
			INSERT INTO
				StaticPage(
					eventId,
					name,
					title,
					content
				)
			VALUES(
				:eventId,
				:name,
				:title,
				:content
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'name',
			'title',
			'content'
		));
		
		$this->execute($sql, $params, 'Create static event page.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, pageIds]
	 */
	public function deletePages($params) { 
		$sql = '
			DELETE FROM
				StaticPage
			WHERE
				id IN (:[pageIds])
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'pageIds'));
		
		$this->execute($sql, $params, 'Delete static event page.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, name, title, content]
	 */
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
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'id',
			'name',
			'title',
			'content'
		));
		
		$this->execute($sql, $params, 'Save static event page.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$sql = '
			DELETE FROM
				StaticPage
			WHERE
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		$this->execute($sql, $params, 'Delete static pages by event ID.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, pageId]
	 */
	public function findByIdAndEvent($params) {
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
				StaticPage.id = :pageId
			AND
				Event.id = :eventId				
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'pageId'));
		
		return $this->queryUnique($sql, $params, 'Find static page.');
	}
}