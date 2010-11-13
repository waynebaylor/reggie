<?php

class db_PageSectionManager extends db_OrderableManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();	
	}
	
	protected function getTableName() {
		return 'Section';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
				
		// set content
		if(model_Section::containsRegTypes($obj)) {
			$obj['content'] = db_RegTypeManager::getInstance()->findBySection($obj);
		}
		else if(model_Section::containsContactFields($obj)) {
			$obj['content'] = db_ContactFieldManager::getInstance()->findBySection($obj);	
		}
		else if(model_Section::containsRegOptions($obj)) {
			$obj['content'] = db_SectionRegOptionGroupManager::getInstance()->findBySection($obj);
		}
		else if(model_Section::containsVariableQuantityOptions($obj)) {
			$obj['content'] = db_VariableQuantityOptionManager::getInstance()->findBySection($obj);
		}
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_PageSectionManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				Section.id,
				Section.pageId,
				Section.name,
				Section.text,
				Section.numbered,
				ContentType.id as contentType_id,
				ContentType.name as contentType_name,
				Section.displayOrder
			FROM
				Section
			INNER JOIN
				ContentType
			ON
				Section.contentTypeId=ContentType.id
			WHERE
				Section.id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find page section.');
	}
	
	public function findByPage($page) {
		$sql = '
			SELECT
				Section.id,
				Section.pageId,
				Section.name,
				Section.text,
				Section.numbered,
				ContentType.id as contentType_id,
				ContentType.name as contentType_name,
				Section.displayOrder
			FROM
				Section
			INNER JOIN
				ContentType
			ON
				Section.contentTypeId=ContentType.id
			WHERE
				pageId=:pageId
			ORDER BY
				displayOrder
		';
		
		$params = array(
			'pageId' => $page['id']	
		);
		
		return $this->query($sql, $params, 'Find page sections.');
	}	
	
	public function createSection($page, $name, $contentTypeId) {
		$sql = '
			INSERT INTO
				Section(
					pageId,
					name,
					contentTypeId,
					displayOrder
				)
			VALUES(
				:pageId,
				:name,
				:contentTypeId,
				:displayOrder
			)
		';	
		
		$params = array(
			'pageId' => $page['id'],
			'name' => $name,
			'contentTypeId' => $contentTypeId,
			'displayOrder' => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Create page section.');
	}
	
	public function save($section) {
		$sql = '
			UPDATE
				Section
			SET
				name = :name,
				text = :text,
				numbered = :numbered
			WHERE
				id=:id				
		';
		
		$params = array(
			'id' => $section['id'],
			'name' => $section['name'],
			'text' => $section['text'],
			'numbered' => $section['numbered']
		);
		
		$this->execute($sql, $params, 'Save page section.');
	}
	
	public function delete($section) {
		$sql = '
			DELETE FROM
				Section
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $section['id']
		);
		
		$this->execute($sql, $params, 'Delete page section.');
	}
	
	public function moveSectionUp($section) {
		$this->moveUp($section, 'pageId', $section['pageId']);
	}
	
	public function moveSectionDown($section) {
		$this->moveDown($section, 'pageId', $section['pageId']);
	}
}

?>