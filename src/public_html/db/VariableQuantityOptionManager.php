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
		
		$obj['prices'] = db_RegOptionPriceManager::getInstance()->findByVariableQuantityOption(array(
			'eventId' => $obj['eventId'],
			'variableQuantityId' => $obj['id']
		));
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_VariableQuantityOptionManager();
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
				sectionId,
				code,
				description,
				capacity,
				displayOrder
			FROM
				VariableQuantityOption
			WHERE
				eventId = :eventId
			AND
				id = :id
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find variable quantity option.');
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
				code,
				description,
				capacity,
				displayOrder
			FROM
				VariableQuantityOption
			WHERE
				eventId = :eventId
			AND
				id = :id
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->rawQueryUnique($sql, $params, 'Find variable quantity option info.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, sectionId]
	 */
	public function findBySection($params) {
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
				eventId = :eventId
			AND
				sectionId = :sectionId
			ORDER BY
				displayOrder
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'sectionId'));
		
		return $this->query($sql, $params, 'Find variable quantity options in section.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, sectionId, code, description, capacity]
	 */
	public function createOption($params) {
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
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'sectionId',
			'code',
			'description',
			'capacity'
		));
		$params['displayOrder'] = $this->getNextOrder();
		
		$this->execute($sql, $params, 'Create variable quantity option.');
		
		return $this->lastInsertId();
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function delete($params) {
		$varOpt = db_VariableQuantityOptionManager::getInstance()->find($params);
		
		// delete price associations.
		$sql = '
			DELETE FROM
				VariableQuantityOption_RegOptionPrice
			WHERE
				variableQuantityId = :id
			AND
				variableQuantityId 
			IN (
				SELECT id 
				FROM VariableQuantityOption
				WHERE eventId = :eventId
				AND id = :id
			)
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $p, 'Delete option price associations.');
		
		// delete prices.
		foreach($varOpt['prices'] as $price) {
			db_RegOptionPriceManager::getInstance()->delete($price);
		}
		
		// delete option.
		$sql = '
			DELETE FROM
				VariableQuantityOption
			WHERE
				eventId = :eventId
			AND
				id = :id
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete variable quantity option.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, code, description, capacity]
	 */
	public function save($option) {
		$sql = '
			UPDATE
				VariableQuantityOption
			SET
				code = :code,
				description = :description,
				capacity = :capacity
			WHERE
				eventId = :eventId
			AND
				id = :id
		';
		
		$params = ArrayUtil::keyIntersect($option, array('eventId', 'id', 'code', 'description', 'capacity'));
		
		$this->execute($sql, $params, 'Save variable quantity option.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function moveOptionUp($params) {
		$varOpt = db_VariableQuantityOptionManager::getInstance()->findInfo($params);
		$this->moveUp($varOpt, 'sectionId', $varOpt['sectionId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function moveOptionDown($params) {
		$varOpt = db_VariableQuantityOptionManager::getInstance()->findInfo($params);
		$this->moveDown($params, 'sectionId', $params['sectionId']);
	}
}

?>