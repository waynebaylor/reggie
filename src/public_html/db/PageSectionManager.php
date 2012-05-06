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
			$obj['content'] = db_RegTypeManager::getInstance()->findBySection(array(
				'eventId' => $obj['eventId'],
				'sectionId' => $obj['id']
			));
		}
		else if(model_Section::containsContactFields($obj)) {
			$obj['content'] = db_ContactFieldManager::getInstance()->findBySection(array(
				'eventId' => $obj['eventId'],
				'sectionId' => $obj['id']
			));	
		}
		else if(model_Section::containsRegOptions($obj)) {
			$obj['content'] = db_GroupManager::getInstance()->findBySectionId(array(
				'eventId' => $obj['eventId'],
				'sectionId' => $obj['id']
			));
		}
		else if(model_Section::containsVariableQuantityOptions($obj)) {
			$obj['content'] = db_VariableQuantityOptionManager::getInstance()->findBySection(array(
				'eventId' => $obj['eventId'],
				'sectionId' => $obj['id']
			));
		}
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_PageSectionManager();
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
				Section.id,
				Section.eventId,
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
				Section.contentTypeId = ContentType.id
			WHERE
				Section.id = :id
			AND
				Section.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find page section.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function findSectionInfo($params) {
		$sql = '
			SELECT
				Section.id,
				Section.eventId,
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
				Section.contentTypeId = ContentType.id
			WHERE
				Section.id = :id
			AND
				Section.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->rawQueryUnique($sql, $params, 'Find page section info.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, pageId]
	 */
	public function findByPage($params) {
		$sql = '
			SELECT
				Section.id,
				Section.eventId,
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
				Section.contentTypeId = ContentType.id
			WHERE
				pageId = :pageId
			AND
				Section.eventId = :eventId
			ORDER BY
				displayOrder
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'pageId'));
		
		return $this->query($sql, $params, 'Find page sections.');
	}	
	
	/**
	 * 
	 * @param array $params [eventId, pageId, name, contentTypeId]
	 */
	public function createSection($params) {
		$sql = '
			INSERT INTO
				Section(
					eventId,
					pageId,
					name,
					contentTypeId,
					displayOrder
				)
			VALUES(
				:eventId,
				:pageId,
				:name,
				:contentTypeId,
				:displayOrder
			)
		';	
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'pageId',
			'name',
			'contentTypeId'	
		));
		$params['displayOrder'] = $this->getNextOrder();
		
		$this->execute($sql, $params, 'Create page section.');
		
		return $this->lastInsertId();
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, name, text, numbered]
	 */
	public function save($params) { 
		$sql = '
			UPDATE
				Section
			SET
				name = :name,
				text = :text,
				numbered = :numbered
			WHERE
				id = :id
			AND
				eventId = :eventId				
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'id',
			'name',
			'text',
			'numbered'
		));
		
		$this->execute($sql, $params, 'Save page section.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, contentType, content]
	 */
	public function delete($params) {
		if(model_Section::containsContactFields($params)) {
			foreach($params['content'] as $field) {
				// overwrite eventId to ensure security checks are consistent.
				$field['eventId'] = $params['eventId'];  
				db_ContactFieldManager::getInstance()->delete($field);
			}
		}
		else if(model_Section::containsRegOptions($params)) {
			foreach($params['content'] as $optGroup) {
				// overwrite eventId to ensure security checks are consistent.
				$optGroup['eventId'] = $params['eventId'];
				db_GroupManager::getInstance()->delete($optGroup);
			}
		}
		else if(model_Section::containsRegTypes($params)) {
			foreach($params['content'] as $regType) {
				// overwrite eventId to ensure security checks are consistent.
				$regType['eventId'] = $params['eventId'];
				db_RegTypeManager::getInstance()->delete(array(
					'eventId' => $regType['eventId'],
					'regTypeId' => $regType['id']
				));
			}
		}
		else if(model_Section::containsVariableQuantityOptions($params)) {
			foreach($params['content'] as $opt) {
				// overwrite eventId to ensure security checks are consistent.
				$opt['eventId'] = $params['eventId'];
				db_VariableQuantityOptionManager::getInstance()->delete($opt);
			}
		}
		
		// delete section.
		$sql = '
			DELETE FROM
				Section
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete page section.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function moveSectionUp($params) {
		$sectionInfo = $this->findSectionInfo($params);
		$this->moveUp($sectionInfo, 'pageId', $sectionInfo['pageId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function moveSectionDown($params) {
		$sectionInfo = $this->findSectionInfo($params);
		$this->moveDown($sectionInfo, 'pageId', $sectionInfo['pageId']);
	}
}

?>