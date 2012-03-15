<?php

class db_RegOptionManager extends db_OrderableManager
{
	private static $instance;
	
	private $optionPriceManager;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'RegOption';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		$obj['groups'] = db_GroupManager::getInstance()->findByOptionId($obj['id']);
		$obj['prices'] = db_RegOptionPriceManager::getInstance()->findByRegOption($obj);

		return $obj;
	}
	
	public static function getInstance() {
		if(!isset(self::$instance)) {
			self::$instance = new db_RegOptionManager();
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
				parentGroupId,
				code,
				description,
				capacity,
				defaultSelected,
				showPrice,
				displayOrder,
				text
			FROM
				RegOption
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find reg option.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, parentGroupId]
	 */
	public function findByGroup($params) {
		$sql = '
			SELECT
				id,
				eventId,
				parentGroupId,
				code,
				description,
				capacity,
				defaultSelected,
				showPrice,
				displayOrder,
				text
			FROM
				RegOption
			WHERE
				parentGroupId = :parentGroupId
			AND
				eventId = :eventId
			ORDER BY
				displayOrder
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'parentGroupId'));
		
		return $this->query($sql, $params, 'Find reg options by group.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, parentGroupId, code, description, capacity, defaultSelected, showPrice]
	 */
	public function createRegOption($params) {
		$sql = '
			INSERT INTO
				RegOption(
					eventId,
					parentGroupId,
					code,
					description,
					capacity,
					defaultSelected,
					showPrice,
					displayOrder	
				)
			VALUES(
				:eventId,
				:parentGroupId,
				:code,
				:description,
				:capacity,
				:defaultSelected,
				:showPrice,
				:displayOrder
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'parentGroupId',
			'code',
			'description',
			'capacity',
			'defaultSelected',
			'showPrice'
		));
		$params['displayOrder'] = $this->getNextOrder();
		
		$this->execute($sql, $params, 'Create reg option.');
		
		return $this->lastInsertId();
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, text]
	 */
	public function saveText($params) {
		$sql = '
			UPDATE
				RegOption
			SET
				text = :text
			WHERE
				id = :id
			AND
				eventId = :eventId
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id', 'text'));
		
		$this->execute($sql, $params, 'Save reg option.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, code, description, capacity, defaultSelected, showPrice]
	 */
	public function save($params) {
		$sql = '
			UPDATE
				RegOption
			SET
				code = :code,
				description = :description,
				capacity = :capacity,
				defaultSelected = :defaultSelected,
				showPrice = :showPrice
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'id', 
			'eventId',
			'code', 
			'description', 
			'capacity', 
			'defaultSelected', 
			'showPrice'
		)); 
		
		$this->execute($sql, $params, 'Save reg option.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function delete($params) {
		// delete the option's groups.
		$option = $this->find($params);
		foreach($option['groups'] as $group) {
			db_GroupManager::getInstance()->deleteById($group['id']);
		}		
		
		// delete the price associations.
		$sql = '
			DELETE FROM
				RegOption_RegOptionPrice
			WHERE
				RegOption_RegOptionPrice.regOptionId = :regOptionId
			AND
				RegOption_RegOptionPrice.regOptionId
			IN (
				SELECT RegOption.id
				FROM RegOption
				WHERE RegOption.eventId = :eventId
			)
		';
		
		$p = array(
			'eventId' => $params['eventId'],
			'regOptionId' => $params['id']
		);
		
		$this->execute($sql, $p, 'Delete option price associations.');
		
		// delete the option's prices.
		foreach($option['prices'] as $price) {
			db_RegOptionPriceManager::getInstance()->delete($price);
		}

		// delete the option.
		$sql = '
			DELETE FROM
				RegOption
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete reg option.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, parentGroupId, displayOrder]
	 */
	public function moveOptionUp($params) {
		$this->checkRegOptionPermission($params);
		
		$this->moveUp($params, 'parentGroupId', $params['parentGroupId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, parentGroupId, displayOrder]
	 */
	public function moveOptionDown($params) {
		$this->checkRegOptionPermission($params);
		
		$this->moveDown($params, 'parentGroupId', $params['parentGroupId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, parentGroupId, text]
	 */
	public function createText($params) {
		$sql = '
			INSERT INTO
				RegOption(
					eventId,
					parentGroupId,
					code,
					description,
					capacity,
					defaultSelected,
					showPrice,
					displayOrder,
					text	
				)
			VALUES(
				:eventId,
				:parentGroupId,
				:code,
				:description,
				:capacity,
				:defaultSelected,
				:showPrice,
				:displayOrder,
				:text
			)
		';
		
		$params = array(
			'eventId' => $params['eventId'],
			'parentGroupId' => $params['parentGroupId'],
			'code' => 'REGGIE_TEXT_'.time(),
			'description' => '',
			'capacity' => 0,
			'defaultSelected' => 'F',
			'showPrice' => 'F',
			'displayOrder' => $this->getNextOrder(),
			'text' => $params['text']
		);
		
		$this->execute($sql, $params, 'Create text.');
		
		return $this->lastInsertId();
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	private function checkRegOptionPermission($params) {
		$sql = '
			SELECT
				id,
				eventId
			FROM
				RegOption
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$results = $this->rawQuery($sql, $params, 'Check reg option permission.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to access RegOption. (event id, reg opt id) -> ({$params['eventId']}, {$params['id']}).");
		}
	}
} 

?>