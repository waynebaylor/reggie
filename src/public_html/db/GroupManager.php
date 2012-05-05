<?php

class db_GroupManager extends db_OrderableManager
{
	private static $instance;
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_GroupManager();
		}
		
		return self::$instance;
	}
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'RegOptionGroup';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);

		$obj['options'] = db_RegOptionManager::getInstance()->findByGroup(array(
			'eventId' => $obj['eventId'],
			'parentGroupId' => $obj['id']
		));
		
		return $obj;
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
				sectionId,
				regOptionId,
				required,
				multiple,
				minimum,
				maximum,
				displayOrder
			FROM
				RegOptionGroup
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find reg option group.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, sectionId]
	 */
	public function findBySectionId($params) {
		$sql = '
			SELECT
				id,
				eventId,
				sectionId,
				regOptionId,
				required,
				multiple,
				minimum,
				maximum,
				displayOrder
			FROM
				RegOptionGroup
			WHERE
				sectionId = :sectionId
			AND
				eventId = :eventId
			ORDER BY
				displayOrder
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'sectionId'));
		
		return $this->query($sql, $params, 'Find reg option groups in section.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, optionId]
	 */
	public function findByOptionId($params) {
		$sql = '
			SELECT
				id,
				eventId,
				sectionId,
				regOptionId,
				required,
				multiple,
				minimum,
				maximum,
				displayOrder
			FROM
				RegOptionGroup
			WHERE
				regOptionId = :optionId
			AND
				eventId = :eventId
			ORDER BY
				displayOrder
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'optionId'));
		
		return $this->query($sql, $params, 'Find reg option groups in option.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function moveGroupUp($params) {
		$group = db_GroupManager::getInstance()->findInfo($params);
		
		if(empty($group['sectionId'])) {
			$this->moveUp($group, 'regOptionId', $group['regOptionId']);
		}
		else {
			$this->moveUp($group, 'sectionId', $group['sectionId']);
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function moveGroupDown($params) {
		$group = db_GroupManager::getInstance()->findInfo($params);
		
		if(empty($group['sectionId'])) {
			$this->moveDown($group, 'regOptionId', $group['regOptionId']);
		}
		else {
			$this->moveDown($group, 'sectionId', $group['sectionId']);
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, sectionId, required, multiple, minimum, maximum]
	 */
	public function createGroupUnderSection($params) {
		$this->checkSectionPermission($params);
		
		$sql = '
			INSERT INTO
				RegOptionGroup(
					eventId,
					sectionId,
					required,
					multiple,
					minimum,
					maximum,
					displayOrder
				)
			VALUES(
				:eventId,
				:sectionId,
				:required,
				:multiple,
				:minimum,
				:maximum,
				:displayOrder
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId', 
			'sectionId', 
			'required', 
			'multiple', 
			'minimum', 
			'maximum'
		));
		$params['displayOrder'] = $this->getNextOrder();
		
		$this->execute($sql, $params, 'Create reg option group under section.');
		
		return $this->lastInsertId();
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionId, required, multiple, minimum, maximum]
	 */
	public function createGroupUnderOption($params) {
		$this->checkOptionPermission($params);
		
		$sql = '
			INSERT INTO
				RegOptionGroup(
					eventId,
					regOptionId,
					required,
					multiple,
					minimum,
					maximum,
					displayOrder
				)
			VALUES(
				:eventId,
				:regOptionId,
				:required,
				:multiple,
				:minimum,
				:maximum,
				:displayOrder
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId', 
			'regOptionId', 
			'required', 
			'multiple', 
			'minimum', 
			'maximum'
		));
		$params['displayOrder'] = $this->getNextOrder();
		
		$this->execute($sql, $params, 'Create reg option group under option.');
		
		return $this->lastInsertId();
	}
	
	/**
	 * 
	 * @param array $params [eventId, (sectionId|regOptionId), required, multiple, minimum, maximum]
	 */
	public function createGroup($params) {
		if(empty($group['sectionId'])) {
			return $this->createGroupUnderOption($group);
		}
		else {
			return $this->createGroupUnderSection($group);
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function deleteById($params) {
		// delete the group's options.
		$group = $this->find($params);
		foreach($group['options'] as $option) {
			db_RegOptionManager::getInstance()->delete($option);			
		}
		
		// delete the group.
		$sql = '
			DELETE FROM
				RegOptionGroup
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete reg option group.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function delete($params) {
		$this->deleteById($params);
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, required, multiple, minimum, maximum]
	 */
	public function save($params) {
		$sql = '
			UPDATE
				RegOptionGroup
			SET
				required = :required,
				multiple = :multiple,
				minimum = :minimum,
				maximum = :maximum
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'id',
			'required',
			'multiple',
			'minimum',
			'maximum'
		));
		
		$this->execute($sql, $params, 'Save section reg option group.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function findInfo($params) {
		$sql = '
			SELECT
				id,
				eventId,
				sectionId,
				regOptionId,
				required,
				multiple,
				minimum,
				maximum,
				displayOrder
			FROM
				RegOptionGroup
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->rawQueryUnique($sql, $params, 'Find reg option group info.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, sectionId]
	 */
	private function checkSectionPermission($params) {
		$results = $this->rawSelect(
			'Section',
			array('eventId', 'id'),
			array(
				'eventId' => $params['eventId'], 
				'id' => $params['sectionId']
			)
		);
		
		if(count($results) == 0) {
			$msg = "Permission denied to create option group in section.";
			$msg .= " (event id, section id) -> ({$params['eventId']}, {$params['sectionId']})";
			throw new Exception($msg);
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionId]
	 */
	private function checkOptionPermission($params) {
		$results = $this->rawSelect(
			'RegOption',
			array('eventId', 'id'),
			array(
				'eventId' => $params['eventId'], 
				'id' => $params['regOptionId']
			)
		);
		
		if(count($results) == 0) {
			$msg = "Permission denied to create option group in option.";
			$msg .= " (event id, option id) -> ({$params['eventId']}, {$params['regOptionId']})";
			throw new Exception($msg);
		}
	}
}

?>