<?php

require_once 'db/Manager.php';

abstract class db_OrderableManager extends db_Manager
{
	protected function __construct() {
		parent::__construct();
	}
	
	public function getNextOrder() {
		$sql = "
			SELECT 
				MAX(displayOrder) as maxOrder
			FROM
				{$this->getTableName()}
		";
		
		$maxOrder = $this->rawQueryUnique($sql, array(), 'Find max display order.');
		$maxOrder = $maxOrder['maxOrder'];
		
		return max(1, $maxOrder+1);
	}
	
	public function swapOrder($obj1, $obj2) {
		// set obj1 order to -1.
		$sql = "
			UPDATE
				{$this->getTableName()}
			SET
				displayOrder=-1
			WHERE
				id=:id		
		";
				
		$params = array(
			'id' => $obj1['id']
		);
		
		$this->execute($sql, $params, 'Swapping display order.');
		
		// set obj2 order to obj1's original order.
		$sql = "
			UPDATE
				{$this->getTableName()}
			SET
				displayOrder=:displayOrder
			WHERE
				id=:id
		";
				
		$params = array(
			'id' => $obj2['id'],
			'displayOrder' => $obj1['displayOrder']
		);
		
		$this->execute($sql, $params, 'Swapping display order.');
		
		// set obj1 order to obj2's original order.
		$params = array(
			'id' => $obj1['id'],
			'displayOrder' => $obj2['displayOrder']
		);
		
		$this->execute($sql, $params, 'Swapping display order.');
	}
	
	public function moveUp($obj, $restrictField, $restrictValue) {
		$sql = "
			SELECT
				id,
				displayOrder
			FROM
				{$this->getTableName()}
			WHERE
				{$restrictField}=:restrictValue
			ORDER BY
				displayOrder
		";
				
		$params = array(
			'restrictValue' => $restrictValue
		);
				
		$objs = $this->rawQuery($sql, $params, 'Find objects by display order.'); 
		foreach($objs as $index => $o) {
			if(intval($index, 10) === 0 && intval($o['id'], 10) === intval($obj['id'], 10)) {
				return;
			}
			else if(intval($o['id'], 10) === intval($obj['id'], 10)) {
				$this->swapOrder($objs[$index-1], $obj);
				return;
			}
		}
	}
	
	public function moveDown($obj, $restrictField, $restrictValue) {
		$sql = "
			SELECT
				id,
				displayOrder
			FROM
				{$this->getTableName()}
			WHERE
				{$restrictField}=:restrictValue
			ORDER BY
				displayOrder
		";

		$params = array(
			'restrictValue' => $restrictValue
		);
				
		$objs = $this->rawQuery($sql, $params, 'Find objects by display order.');
		foreach($objs as $index => $o) {
			if(intval($index, 10) === count($objs)-1 && intval($o['id'], 10) === intval($obj['id'], 10)) {
				return;
			}
			else if(intval($o['id'], 10) === intval($obj['id'], 10)) {
				$this->swapOrder($objs[$index+1], $obj);
				return;
			}
		}
	}
}