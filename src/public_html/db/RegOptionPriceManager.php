<?php

class db_RegOptionPriceManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}	
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);

		// remove the "seconds" from the datetimes.
		$obj['startDate'] = substr($obj['startDate'], 0, -3);
		$obj['endDate'] = substr($obj['endDate'], 0, -3);
		
		$obj['visibleToAll'] = $this->isVisibleToAllRegTypes($obj);     
		$regTypes = db_RegTypeManager::getInstance()->findByPrice($obj);
		$obj['visibleTo'] = $regTypes;
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_RegOptionPriceManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$price = $this->findRegOptionPrice($id);
		
		if(empty($price)) {
			$price = $this->findVariableQuantityPrice($id);
		}
		
		return $price;
	}
	
	public function findByVariableQuantityOption($option) {
		$sql = '
			SELECT
				RegOptionPrice.id,
				RegOptionPrice.eventId,
				VariableQuantityOption_RegOptionPrice.variableQuantityId as regOptionId,
				RegOptionPrice.description,
				RegOptionPrice.startDate,
				RegOptionPrice.endDate,
				RegOptionPrice.price
			FROM
				RegOptionPrice
			INNER JOIN
				VariableQuantityOption_RegOptionPrice
			ON
				RegOptionPrice.id = VariableQuantityOption_RegOptionPrice.regOptionPriceId
			WHERE
				VariableQuantityOption_RegOptionPrice.variableQuantityId = :id
			ORDER BY
				RegOptionPrice.startDate
		';

		$params = array(
			'id' => $option['id']
		);
		
		return $this->query($sql, $params, 'Find prices by variable quantity option.');
	}
	
	public function findByRegOption($option) {
		$sql = '
			SELECT
				RegOptionPrice.id,
				RegOptionPrice.eventId,
				RegOption_RegOptionPrice.regOptionId,
				RegOptionPrice.description,
				RegOptionPrice.startDate,
				RegOptionPrice.endDate,
				RegOptionPrice.price
			FROM
				RegOptionPrice
			INNER JOIN
				RegOption_RegOptionPrice
			ON
				RegOptionPrice.id = RegOption_RegOptionPrice.regOptionPriceId
			WHERE
				RegOption_RegOptionPrice.regOptionId = :id
			ORDER BY
				RegOptionPrice.startDate
		';
		
		$params = array(
			'id' => $option['id']
		);
		
		return $this->query($sql, $params, 'Find prices by reg option.');
	}
	
	public function createVariableQuantityPrice($price) {
		$priceId = $this->createPrice($price);
		
		//
		// create price variable quantity option association.
		$this->setVariableQuantity($price['regOptionId'], $priceId);

		//
		// create price reg type associations.
		$regTypeIds = $price['regTypeIds'];
		$this->setRegTypes($priceId, $regTypeIds);	
	}
	
	public function createRegOptionPrice($price) {
		$priceId = $this->createPrice($price);
		
		//
		// create price reg option association.
		//
		$this->setRegOption($price['regOptionId'], $priceId);
		
		//
		// create price reg type associations.
		//
		$regTypeIds = $price['regTypeIds'];
		$this->setRegTypes($priceId, $regTypeIds);
	}
	
	private function createPrice($price) {
		$sql = '
			INSERT INTO
				RegOptionPrice(
					eventId,
					description,
					startDate,
					endDate,
					price
				)
			VALUES(
				:eventId,
				:description,
				:startDate,
				:endDate,
				:price
			)	
		';
		
		$params = array(
			'eventId' => $price['eventId'],
			'description' => $price['description'],
			'startDate' => $price['startDate'],
			'endDate' => $price['endDate'],
			'price' => $price['price']
		);
		
		$this->execute($sql, $params, 'Create reg option price.');

		return $this->lastInsertId();
	}
	
	public function delete($price) {
		$sql = '
			DELETE FROM
				RegOptionPrice
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $price['id']
		);
		
		$this->execute($sql, $params, 'Delete reg option price.');
	}
	
	public function save($price) {
		$sql = '
			UPDATE
				RegOptionPrice
			SET
				description=:description,
				startDate=:startDate,
				endDate=:endDate,
				price=:price
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $price['id'],
			'description' => $price['description'],
			'startDate' => $price['startDate'],
			'endDate' => $price['endDate'],
			'price' => $price['price']
		);
		
		$this->execute($sql, $params, 'Save reg option price.');
		
		$this->removeRegTypes($price['id']);
		$this->setRegTypes($price['id'], $price['regTypeIds']);
	}
	
	public function isVariableQuantityPrice($price) {
		$sql = '
			SELECT 
				count(*) as count
			FROM
				VariableQuantityOption_RegOptionPrice
			WHERE
				regOptionPriceId = :id
		';

		$params = array(
			'id' => $price['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if price is associated with a variable quantity option.');
		return ($result['count'] > 0);
	}
	
	private function isVisibleToAllRegTypes($price) {
		$sql = '
			SELECT
				regOptionPriceId
			FROM 
				RegType_RegOptionPrice
			WHERE
				regOptionPriceId = :id
			AND
				regTypeId is NULL
		';
		
		$params = array(
			'id' => $price['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if reg option price is visible to all reg types.');

		return !empty($result);
	}
	
	private function removeRegTypes($priceId) {
		$sql = '
			DELETE FROM
				RegType_RegOptionPrice
			WHERE
				regOptionPriceId = :id
		';
		
		$params = array(
			'id' => $priceId
		);
		
		$this->execute($sql, $params, 'Remove all reg types from reg option price visibility.');
	}
	
	private function setRegTypes($priceId, $regTypeIds) {
		if(in_array(-1, $regTypeIds)) {
			// price visible to ALL reg types. so we
			// want the regTypeId to be NULL.
			$sql = '
				INSERT INTO
					RegType_RegOptionPrice(
						regOptionPriceId
					)
				VALUES(
					:regOptionPriceId
				)
			';
			
			$params = array(
				'regOptionPriceId' => $priceId
			);
			
			$this->execute($sql, $params, 'Set reg option price visibile to reg types.');
		}
		else {
			foreach($regTypeIds as $regTypeId) {
				$sql = '
					INSERT INTO
						RegType_RegOptionPrice(
							regTypeId,
							regOptionPriceId	
						)
					VALUES(
						:regTypeId,
						:regOptionPriceId
					)
				';
				
				$params = array(
					'regTypeId' => $regTypeId,
					'regOptionPriceId' => $priceId
				);
				
				$this->execute($sql, $params, 'Set reg option price visibile to reg types.');
			}
		}
	}
	
	private function setRegOption($regOptionId, $priceId) {
		$sql = '
			INSERT INTO
				RegOption_RegOptionPrice(
					regOptionId,
					regOptionPriceId
				)
			VALUES(
				:regOptionId,
				:regOptionPriceId
			)
		';
		
		$params = array(
			'regOptionId' => $regOptionId,
			'regOptionPriceId' => $priceId
		);
		
		$this->execute($sql, $params, 'Create reg option/price association.');
	}
	
	private function setVariableQuantity($optionId, $priceId) {
		$sql = '
			INSERT INTO
				VariableQuantityOption_RegOptionPrice(
					variableQuantityId,
					regOptionPriceId
				)
			VALUES(
				:variableQuantityId,
				:regOptionPriceId
			)
		';

		$params = array(
			'variableQuantityId' => $optionId,
			'regOptionPriceId' => $priceId
		);
		
		$this->execute($sql, $params, '');
	}
	
	private function findVariableQuantityPrice($id) {
		$sql = '
			SELECT
				RegOptionPrice.id,
				RegOptionPrice.eventId,
				VariableQuantityOption_RegOptionPrice.variableQuantityId as regOptionId,
				RegOptionPrice.description,
				RegOptionPrice.startDate,
				RegOptionPrice.endDate,
				RegOptionPrice.price
			FROM
				RegOptionPrice
			INNER JOIN
				VariableQuantityOption_RegOptionPrice
			ON
				RegOptionPrice.id = VariableQuantityOption_RegOptionPrice.regOptionPriceId
			WHERE
				RegOptionPrice.id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find variable quantity option price.');
	}
	
	private function findRegOptionPrice($id) {
		$sql = '
			SELECT
				RegOptionPrice.id,
				RegOptionPrice.eventId,
				RegOption_RegOptionPrice.regOptionId,
				RegOptionPrice.description,
				RegOptionPrice.startDate,
				RegOptionPrice.endDate,
				RegOptionPrice.price
			FROM
				RegOptionPrice
			INNER JOIN
				RegOption_RegOptionPrice
			ON
				RegOptionPrice.id = RegOption_RegOptionPrice.regOptionPriceId
			WHERE
				RegOptionPrice.id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find reg option price.');
	}
}

?>