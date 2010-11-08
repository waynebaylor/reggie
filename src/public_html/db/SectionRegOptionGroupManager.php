<?php

class db_SectionRegOptionGroupManager extends db_GroupManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Section_RegOptionGroup';
	}
	
	protected function createMappingRow($group) {
		$sql = '
			INSERT INTO
				Section_RegOptionGroup(
					sectionId,
					optionGroupId,
					displayOrder	
				)
			VALUES(
				:sectionId,
				:optionGroupId,
				:displayOrder
			)
		';
		
		$params = array(
			'sectionId'     => $group['sectionId'],
			'optionGroupId' => $this->lastInsertId(),
			'displayOrder'  => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Link reg option group to section.');
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_SectionRegOptionGroupManager();
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
				Section_RegOptionGroup.sectionId,
				Section_RegOptionGroup.displayOrder
			FROM
				RegOptionGroup
			INNER JOIN
				Section_RegOptionGroup
			ON
				RegOptionGroup.id=Section_RegOptionGroup.optionGroupId
			WHERE
				RegOptionGroup.id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find section reg option group.');
	}
	
	public function findBySection($section) {
		$sql = '
			SELECT
				RegOptionGroup.id,
				RegOptionGroup.description,
				RegOptionGroup.required,
				RegOptionGroup.multiple,
				RegOptionGroup.minimum,
				RegOptionGroup.maximum,
				Section_RegOptionGroup.sectionId,
				Section_RegOptionGroup.displayOrder
			FROM
				RegOptionGroup
			INNER JOIN
				Section_RegOptionGroup
			ON
				RegOptionGroup.id=Section_RegOptionGroup.optionGroupId
			WHERE
				Section_RegOptionGroup.sectionId=:id
			ORDER BY
				Section_RegOptionGroup.displayOrder
		';
		
		$params = array(
			'id' => $section['id']
		);
		
		return $this->query($sql, $params, 'Find reg option groups in section.');
	}
	
	public function moveGroupUp($group) {
		$sql = '
			SELECT
				id,
				displayOrder
			FROM
				Section_RegOptionGroup
			WHERE
				sectionId=:sectionId
			AND
				optionGroupId=:groupId
		';
		
		$params = array(
			'sectionId' => $group['sectionId'],
			'groupId' => $group['id']
		);
		
		$mappingRow = $this->queryUnique($sql, $params, 'Find mapping row for group.');
		
		$this->moveUp($mappingRow, 'sectionId', $group['sectionId']);
	}
	
	public function moveGroupDown($group) {
		$sql = '
			SELECT
				id,
				displayOrder
			FROM
				Section_RegOptionGroup
			WHERE
				sectionId=:sectionId
			AND
				optionGroupId=:groupId
		';
		
		$params = array(
			'sectionId' => $group['sectionId'],
			'groupId' => $group['id']
		);
		
		$mappingRow = $this->queryUnique($sql, $params, 'Find mapping row for group.');
		
		$this->moveDown($mappingRow, 'sectionId', $group['sectionId']);
	}
}
?>