<?php

class db_RegOptionGroupManager extends db_GroupManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'RegOption_RegOptionGroup';
	}
	
	protected function createMappingRow($group) {
		$sql = '
			INSERT INTO
				RegOption_RegOptionGroup(
					regOptionId,
					optionGroupId,
					displayOrder	
				)
			VALUES(
				:regOptionId,
				:optionGroupId,
				:displayOrder
			)
		';
		
		$params = array(
			'regOptionId'     => $group['regOptionId'],
			'optionGroupId' => $group['id'],
			'displayOrder'  => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Link reg option group to option.');
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_RegOptionGroupManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				RegOptionGroup.id,
				RegOptionGroup.description,
				RegOptionGroup.required,
				RegOptionGroup.multiple,
				RegOptionGroup.minimum,
				RegOptionGroup.maximum,
				RegOption_RegOptionGroup.regOptionId,
				RegOption_RegOptionGroup.displayOrder
			FROM
				RegOptionGroup
			INNER JOIN
				RegOption_RegOptionGroup
			ON
				RegOptionGroup.id=RegOption_RegOptionGroup.optionGroupId
			WHERE
				RegOptionGroup.id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find option reg option group.');
	}
	
	public function findByRegOption($option) {
		$sql = '
			SELECT
				RegOptionGroup.id,
				RegOptionGroup.description,
				RegOptionGroup.required,
				RegOptionGroup.multiple,
				RegOptionGroup.minimum,
				RegOptionGroup.maximum,
				RegOption_RegOptionGroup.regOptionId,
				RegOption_RegOptionGroup.displayOrder
			FROM
				RegOptionGroup
			INNER JOIN
				RegOption_RegOptionGroup
			ON
				RegOptionGroup.id=RegOption_RegOptionGroup.optionGroupId
			WHERE
				RegOption_RegOptionGroup.regOptionId=:id
			ORDER BY
				RegOption_RegOptionGroup.displayOrder
		';
		
		$params = array(
			'id' => $option['id']
		);
		
		return $this->query($sql, $params, 'Find reg option groups in option.');
	}
	
	public function moveGroupUp($group) {
		$sql = '
			SELECT
				id,
				displayOrder
			FROM
				RegOption_RegOptionGroup
			WHERE
				regOptionId=:regOptionId
			AND
				optionGroupId=:groupId
		';
		
		$params = array(
			'regOptionId' => $group['regOptionId'],
			'groupId' => $group['id']
		);
		
		$mappingRow = $this->queryUnique($sql, $params, 'Find mapping row for group.');
		
		$this->moveUp($mappingRow, 'regOptionId', $group['regOptionId']);
	}
	
	public function moveGroupDown($group) {
		$sql = '
			SELECT
				id,
				displayOrder
			FROM
				RegOption_RegOptionGroup
			WHERE
				regOptionId=:regOptionId
			AND
				optionGroupId=:groupId
		';
		
		$params = array(
			'regOptionId' => $group['regOptionId'],
			'groupId' => $group['id']
		);
		
		$mappingRow = $this->queryUnique($sql, $params, 'Find mapping row for group.');
		
		$this->moveDown($mappingRow, 'regOptionId', $group['regOptionId']);
	}
}
?>