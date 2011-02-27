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
		
		$obj['groups'] = db_RegOptionGroupManager::getInstance()->findByRegOption($obj);
		$obj['prices'] = db_RegOptionPriceManager::getInstance()->findByRegOption($obj);

		return $obj;
	}
	
	public static function getInstance() {
		if(!isset(self::$instance)) {
			self::$instance = new db_RegOptionManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				parentGroupId,
				code,
				description,
				capacity,
				defaultSelected,
				showPrice,
				displayOrder
			FROM
				RegOption
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find reg option.');
	}
	
	public function findByGroup($group) {
		$sql = '
			SELECT
				id,
				parentGroupId,
				code,
				description,
				capacity,
				defaultSelected,
				showPrice,
				displayOrder
			FROM
				RegOption
			WHERE
				parentGroupId=:id
			ORDER BY
				displayOrder
		';
		
		$params = array(
			'id' => $group['id']
		);
		
		return $this->query($sql, $params, 'Find reg options by group.');
	}
	
	public function createRegOption($option) {
		$sql = '
			INSERT INTO
				RegOption(
					parentGroupId,
					code,
					description,
					capacity,
					defaultSelected,
					showPrice,
					displayOrder	
				)
			VALUES(
				:parentGroupId,
				:code,
				:description,
				:capacity,
				:defaultSelected,
				:showPrice,
				:displayOrder
			)
		';
		
		$params = array(
			'parentGroupId'   => $option['parentGroupId'],
			'code'            => $option['code'],
			'description'     => $option['description'],
			'capacity'        => $option['capacity'],
			'defaultSelected' => $option['defaultSelected'],
			'showPrice'       => $option['showPrice'],
			'displayOrder'    => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Create reg option.');
	}
	
	public function save($option) {
		$sql = '
			UPDATE
				RegOption
			SET
				code=:code,
				description=:description,
				capacity=:capacity,
				defaultSelected=:defaultSelected,
				showPrice=:showPrice
			WHERE
				id=:id
		';
		
		$params = array(
			'id'              => $option['id'],
			'code'            => $option['code'],
			'description'     => $option['description'],
			'capacity'        => $option['capacity'],
			'defaultSelected' => $option['defaultSelected'],
			'showPrice'       => $option['showPrice']
		);
		
		$this->execute($sql, $params, 'Save reg option.');
	}
	
	public function delete($option) {
		// delete the option's groups.
		$option = $this->find($option['id']);
		foreach($option['groups'] as $group) {
			db_RegOptionGroupManager::getInstance()->delete($group);
		}		
		
		// delete the option's prices.
		foreach($option['prices'] as $price) {
			db_RegOptionPriceManager::getInstance()->delete($price);
		}

		// delete the option.
		$sql = '
			DELETE FROM
				RegOption
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $option['id']
		);
		
		$this->execute($sql, $params, 'Delete reg option.');
	}
	
	public function moveOptionUp($option) {
		$this->moveUp($option, 'parentGroupId', $option['parentGroupId']);
	}
	
	public function moveOptionDown($option) {
		$this->moveDown($option, 'parentGroupId', $option['parentGroupId']);
	}
} 

?>