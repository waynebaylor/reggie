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
	
	public function find($id) {
		$sql = '
			SELECT 
				id,
				eventId,
				title,
				displayOrder
			FROM
				Page
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find page.');
	}
	
	public function findByEvent($event) {
		$sql = '
			SELECT 
				id,
				eventId,
				title,
				displayOrder
			FROM
				Page
			WHERE
				eventId=:eventId
			ORDER BY
				displayOrder
		';
		
		$params = array(
			'eventId' => $event['id']
		);
		
		return $this->query($sql, $params, 'Find pages by event.');
	}
	
	public function createPage($event, $title, $categories) {
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

		$params = array(
			'eventId' => $event['id'],
			'title' => $title,
			'displayOrder' => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Create page.');
		
		$pageId = $this->lastInsertId();
		
		// create the mapping rows.
		$this->makePageAvailableTo($pageId, $categories);
	}
	
	public function savePage($page, $categoryIds) {
		$sql = '
			UPDATE 
				Page
			SET
				title=:title
			WHERE
				id=:id
		';

		$params = array(
			'title' => $page['title'],
			'id' => $page['id']
		);
		
		$this->execute($sql, $params, 'Save page.');
		
		// update category mappings.
		$this->makePageAvailableTo($page['id'], $categoryIds);
	}
	
	public function deletePage($page) {
		$sql = '
			DELETE FROM
				Page
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $page['id']
		);
		
		$this->execute($sql, $params, 'Delete page.');
	}
	
	public function movePageUp($page) {
		$this->moveUp($page, 'eventId', $page['eventId']);
	}
	
	public function movePageDown($page) {
		$this->moveDown($page, 'eventId', $page['eventId']);
	}
	
	private function makePageAvailableTo($pageId, $categoryIds) {
		// remove existing mappings.
		$sql = '
			DELETE FROM
				CategoryPage
			WHERE
				pageId=:pageId
		';
		
		$params = array(
			'pageId' => $pageId
		);
		
		$this->execute($sql, $params, 'Make page unavailable.');
		
		// add new mappings.
		$sql = '
			INSERT INTO
				CategoryPage(
					categoryId,
					pageId	
				) 
			VALUES(
				:categoryId,
				:pageId
			)
		';
		
		foreach($categoryIds as $categoryId) {
			$params = array(
				'categoryId' => $categoryId,
				'pageId' => $pageId
			);
			
			$this->execute($sql, $params, 'Make page available to category.');
		}
	}
}

?>