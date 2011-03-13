<?php

class db_VariableQuantityOptionManager extends db_OrderableManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'VariableQuantityOption';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		$obj['prices'] = db_RegOptionPriceManager::getInstance()->findByVariableQuantityOption($obj);
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_VariableQuantityOptionManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				eventId,
				sectionId,
				code,
				description,
				capacity,
				displayOrder
			FROM
				VariableQuantityOption
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find variable quantity option.');
	}
	
	public function findBySection($section) {
		$sql = '
			SELECT
				id,
				eventId,
				sectionId,
				code,
				description,
				capacity,
				displayOrder
			FROM
				VariableQuantityOption
			WHERE
				sectionId=:sectionId
			ORDER BY
				displayOrder
		';
		
		$params = array(
			'sectionId' => $section['id']
		);
		
		return $this->query($sql, $params, 'Find variable quantity options in section.');
	}
	
	public function createOption($option) {
		$sql = '
			INSERT INTO
				VariableQuantityOption(
					eventId,
					sectionId,
					code,
					description,
					capacity,
					displayOrder
				)
			VALUES(
				:eventId,
				:sectionId,
				:code,
				:description,
				:capacity,
				:displayOrder
			)
		';
		
		$option['displayOrder'] = $this->getNextOrder();
		$params = $option;
		
		$this->execute($sql, $params, 'Create variable quantity option.');
		
		return $this->lastInsertId();
	}
	
	public function delete($option) {
		// delete prices.
		foreach($option['prices'] as $price) {
			db_RegOptionPriceManager::getInstance()->delete($price);
		}
		
		// delete option.
		$sql = '
			DELETE FROM
				VariableQuantityOption
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $option['id']
		);
		
		$this->execute($sql, $params, 'Delete variable quantity option.');
	}
	
	public function save($option) {
		$sql = '
			UPDATE
				VariableQuantityOption
			SET
				code=:code,
				description=:description,
				capacity=:capacity
			WHERE
				id=:id
		';
		
		$params = $option;
		
		$this->execute($sql, $params, 'Save variable quantity option.');
	}
	
	public function moveOptionUp($option) {
		$this->moveUp($option, 'sectionId', $option['sectionId']);
	}
	
	public function moveOptionDown($option) {
		$this->moveDown($option, 'sectionId', $option['sectionId']);
		
	}
}

?>