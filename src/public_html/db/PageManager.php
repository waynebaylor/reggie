<?php

class db_PageManager extends db_OrderableManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Page';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
				
		$obj['visibleTo'] = db_CategoryManager::getInstance()->findByPage($obj);
		$obj['sections'] = db_PageSectionManager::getInstance()->findByPage($obj);
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_PageManager();
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
				title,
				displayOrder
			FROM
				Page
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find page.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function findPageInfo($params) {
		$sql = '
			SELECT
				id,
				eventId,
				title,
				displayOrder
			FROM
				Page
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->rawQueryUnique($sql, $params, 'Find raw page.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEvent($params) {
		return $this->findByEventId($params);
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEventId($params) {
		$sql = '
			SELECT 
				id,
				eventId,
				title,
				displayOrder
			FROM
				Page
			WHERE
				eventId = :eventId
			ORDER BY
				displayOrder
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->query($sql, $params, 'Find pages by event.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, title, categoryIds]
	 */	
	public function createPage($params) {
		// create the Page row.
		$sql = '
			INSERT INTO
				Page(
					eventId,
					title,
					displayOrder
				)
			VALUES(
				:eventId,
				:title,
				:displayOrder
			)
		';

		$p = array(
			'eventId' => $params['eventId'],
			'title' => $params['title'],
			'displayOrder' => $this->getNextOrder()
		);
		
		$this->execute($sql, $p, 'Create page.');
		
		$pageId = $this->lastInsertId();
		
		// create the mapping rows.
		$this->makePageAvailableTo(array(
			'eventId' => $params['eventId'],
			'pageId' => $pageId, 
			'categoryIds' => $params['categoryIds']
		));
		
		return $pageId;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, title, categoryIds]
	 */
	public function savePage($params) {
		$sql = '
			UPDATE 
				Page
			SET
				title = :title
			WHERE
				id = :id
			AND
				eventId = :eventId
		';

		$p = ArrayUtil::keyIntersect($params, array('eventId', 'id', 'title'));
		
		$this->execute($sql, $p, 'Save page.');
		
		// update category mappings.
		$this->makePageAvailableTo(array(
			'eventId' => $params['eventId'],
			'pageId' => $params['id'], 
			'categoryIds' => $params['categoryIds']
		));
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, sections]
	 */
	public function deletePage($params) {
		// delete category page associations.
		$this->makePageAvailableTo(array(
			'eventId' => $params['eventId'],
			'pageId' => $params['id'], 
			'categoryIds' => array()
		));
		
		// delete sections.
		foreach($params['sections'] as $section) {
			db_PageSectionManager::getInstance()->delete($section);
		}
		
		// delete page.
		$sql = '
			DELETE FROM
				Page
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete page.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function movePageUp($params) {
		$pageInfo = $this->findPageInfo($params);
		$this->moveUp($pageInfo, 'eventId', $params['eventId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function movePageDown($params) {
		$pageInfo = db_PageManager::getInstance()->find($params);
		$this->moveDown($pageInfo, 'eventId', $params['eventId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, pageId, categoryIds]
	 */
	private function makePageAvailableTo($params) {
		// remove existing mappings.
		$sql = '
			DELETE FROM
				Category_Page
			WHERE
				Category_Page.pageId = :pageId
			AND
				Category_Page.pageId 
			IN (
				SELECT Page.id
				FROM Page
				WHERE Page.eventId = :eventId
			)
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'pageId'));
		
		$this->execute($sql, $p, 'Make page unavailable.');
		
		// check page permission before inserting category page rows.
		$this->checkPagePermission($params);
		
		// add new mappings.
		$sql = '
			INSERT INTO
				Category_Page(
					categoryId,
					pageId	
				) 
			VALUES(
				:categoryId,
				:pageId
			)
		';
		
		foreach($params['categoryIds'] as $categoryId) {
			$p = array(
				'categoryId' => $categoryId,
				'pageId' => $params['pageId']
			);
			
			$this->execute($sql, $p, 'Make page available to category.');
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$pages = $this->findByEventId($params);
		
		foreach($pages as $page) {
			db_PageManager::getInstance()->deletePage($page);
		}
	}
	
	/**
	 * 
	 * @param array $params [pageId]
	 */
	private function checkPagePermission($params) {
		$results = $this->rawSelect(
			'Page', 
			array(
				'eventId', 
				'id'), 
			array(
				'eventId' => $params['eventId']
			)
		);
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to page. (event id, page id) -> ({$params['eventId']}, {$params['pageId']}).");
		}
	}
}

?>