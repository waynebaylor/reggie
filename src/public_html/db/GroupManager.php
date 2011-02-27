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

		$obj['options'] = db_RegOptionManager::getInstance()->findByGroup($obj);
		
		return $obj;
	}
	
	public function find($id) {
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
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find reg option group.');
	}
	
	public function findBySectionId($sectionId) {
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
				sectionId = :id
			ORDER BY
				displayOrder
		';
		
		$params = array(
			'id' => $sectionId
		);
		
		return $this->query($sql, $params, 'Find reg option groups in section.');
	}
	
	public function findByOptionId($optionId) {
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
				regOptionId = :id
			ORDER BY
				displayOrder
		';
		
		$params = array(
			'id' => $optionId
		);
		
		return $this->query($sql, $params, 'Find reg option groups in option.');
	}
	
	public function moveGroupUp($group) {
		if(empty($group['sectionId'])) {
			$this->moveUp($group, 'regOptionId', $group['regOptionId']);
		}
		else {
			$this->moveUp($group, 'sectionId', $group['sectionId']);
		}
	}
	
	public function moveGroupDown($group) {
		if(empty($group['sectionId'])) {
			$this->moveDown($group, 'regOptionId', $group['regOptionId']);
		}
		else {
			$this->moveDown($group, 'sectionId', $group['sectionId']);
		}
	}
	
	public function createGroupUnderSection($group) {
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
		
		$params = array(
			'eventId' => $group['eventId'],
			'sectionId' => $group['sectionId'],
			'required' => $group['required'],
			'multiple' => $group['multiple'],
			'minimum' => $group['minimum'],
			'maximum' => $group['maximum'],
			'displayOrder' => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Create reg option group under section.');
		
		return $this->lastInsertId();
	}
	
	public function createGroupUnderOption($group) {
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
		
		$params = array(
			'eventId' => $group['eventId'],
			'regOptionId' => $group['regOptionId'],
			'required' => $group['required'],
			'multiple' => $group['multiple'],
			'minimum' => $group['minimum'],
			'maximum' => $group['maximum'],
			'displayOrder' => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Create reg option group under option.');
		
		return $this->lastInsertId();
	}
	
	public function createGroup($group) {
		if(empty($group['sectionId'])) {
			return $this->createGroupUnderOption($group);
		}
		else {
			return $this->createGroupUnderSection($group);
		}
	}
	
	public function deleteById($groupId) {
		// delete the group's options.
		$group = $this->find($groupId);
		foreach($group['options'] as $option) {
			db_RegOptionManager::getInstance()->delete($option);			
		}
		
		// delete the group.
		$sql = '
			DELETE FROM
				RegOptionGroup
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $groupId
		);
		
		$this->execute($sql, $params, 'Delete reg option group.');
	}
	
	public function delete($group) {
		$this->deleteById($group['id']);
	}
	
	public function save($group) {
		$sql = '
			UPDATE
				RegOptionGroup
			SET
				required=:required,
				multiple=:multiple,
				minimum=:minimum,
				maximum=:maximum
			WHERE
				id=:id
		';
		
		$params = array(
			'id'          => $group['id'],
			'required'    => $group['required'],
			'multiple'    => $group['multiple'],
			'minimum'     => $group['minimum'],
			'maximum'     => $group['maximum']
		);
		
		$this->execute($sql, $params, 'Save section reg option group.');
	}
}

?>