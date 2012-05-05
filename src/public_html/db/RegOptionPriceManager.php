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
		$regTypes = db_RegTypeManager::getInstance()->findByPrice(array(
			'eventId' => $obj['eventId'],
			'priceId' => $obj['id'],
			'visibleToAll' => $obj['visibleToAll']
		));
		$obj['visibleTo'] = $regTypes;
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_RegOptionPriceManager();
		}
		
		return self::$instance;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
		$price = $this->findRegOptionPrice($params);
		
		if(empty($price)) {
			$price = $this->findVariableQuantityPrice($params);
		}
		
		return $price;
	}
	
	/**
	 * 
	 * @param array $params [eventId, variableQuantityId]
	 */
	public function findByVariableQuantityOption($params) {
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
				VariableQuantityOption_RegOptionPrice.variableQuantityId = :variableQuantityId
			AND
				RegOptionPrice.eventId = :eventId
			ORDER BY
				RegOptionPrice.startDate
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'variableQuantityId'));
		
		return $this->query($sql, $params, 'Find prices by variable quantity option.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionId]
	 */
	public function findByRegOption($params) {
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
				RegOption_RegOptionPrice.regOptionId = :regOptionId
			AND
				RegOptionPrice.eventId = :eventId
			ORDER BY
				RegOptionPrice.startDate
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'regOptionId'));
		
		return $this->query($sql, $params, 'Find prices by reg option.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionId, description, startDate, endDate, price, regTypeIds]
	 */
	public function createVariableQuantityPrice($params) {
		$priceId = $this->createPrice($params);
		
		//
		// create price variable quantity option association.
		$this->setVariableQuantity(array(
			'eventId' => $params['eventId'],
			'optionId' => $params['regOptionId'], 
			'priceId' => $priceId
		));

		//
		// create price reg type associations.
		$this->setRegTypes(array(
			'eventId' => $params['eventId'],
			'priceId' => $priceId,
			'regTypeIds' => $params['regTypeIds']
		));	
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionId, description, startDate, endDate, price, regTypeIds]
	 */
	public function createRegOptionPrice($params) {
		$priceId = $this->createPrice($params);
		
		//
		// create price reg option association.
		//
		$this->setRegOption(array(
			'eventId' => $params['eventId'],
			'priceId' => $priceId,
			'regOptionId' => $params['regOptionId']
		));
	
		//
		// create price reg type associations.
		//
		$this->setRegTypes(array(
			'eventId' => $params['eventId'],
			'priceId' => $priceId,
			'regTypeIds' => $params['regTypeIds']
		));
	}
	
	/**
	 * 
	 * @param array $params [eventId, description, startDate, endDate, price]
	 */
	private function createPrice($params) {
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
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'description',
			'startDate',
			'endDate',
			'price'
		));
		
		$this->execute($sql, $params, 'Create reg option price.');

		return $this->lastInsertId();
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function delete($params) {
		// delete reg type associations.
		$this->removeRegTypes($params);		
		
		// delete reg option association.
		$sql = '
			DELETE FROM
				RegOption_RegOptionPrice
			WHERE
				RegOption_RegOptionPrice.regOptionPriceId = :id
			AND
				RegOption_RegOptionPrice.regOptionPriceId
			IN (
				SELECT RegOptionPrice.id
				FROM RegOptionPrice
				WHERE RegOptionPrice.eventId = :eventId
			)
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $p, 'Delete reg option association.');
		
		// delete var quantity option association.
		$sql = '
			DELETE FROM
				VariableQuantityOption_RegOptionPrice
			WHERE
				VariableQuantityOption_RegOptionPrice.regOptionPriceId = :id
			AND
				VariableQuantityOption_RegOptionPrice.regOptionPriceId
			IN (
				SELECT VariableQuantityOption.id
				FROM VariableQuantityOption
				WHERE VariableQuantityOption.eventId = :eventId
			)
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $p, 'Delete var quantity option association.');
		
		// delete price.
		$sql = '
			DELETE FROM
				RegOptionPrice
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete reg option price.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, description, startDate, endDate, price, regTypeIds]
	 */
	public function save($params) {
		$sql = '
			UPDATE
				RegOptionPrice
			SET
				description = :description,
				startDate = :startDate,
				endDate = :endDate,
				price = :price
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$p = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'id',
			'description',
			'startDate',
			'endDate',
			'price'
		));
		
		$this->execute($sql, $p, 'Save reg option price.');
		
		$this->removeRegTypes($params);
		$this->setRegTypes(array(
			'eventId' => $params['eventId'],
			'priceId' => $params['id'],
			'regTypeIds' => $params['regTypeIds']
		));
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function isVariableQuantityPrice($params) {
		$sql = '
			SELECT 
				count(*) as count
			FROM
				VariableQuantityOption_RegOptionPrice
			INNER JOIN
				RegOptionPrice
			ON
				VariableQuantityOption_RegOptionPrice.regOptionPriceId = RegOptionPrice.id
			WHERE
				VariableQuantityOption_RegOptionPrice.regOptionPriceId = :id
			AND
				RegOptionPrice.eventId = :eventId
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if price is associated with a variable quantity option.');
		return ($result['count'] > 0);
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	private function isVisibleToAllRegTypes($params) {
		$sql = '
			SELECT
				RegType_RegOptionPrice.regOptionPriceId
			FROM 
				RegType_RegOptionPrice
			INNER JOIN
				RegOptionPrice
			ON
				RegType_RegOptionPrice.regOptionPriceId = RegOptionPrice.id
			WHERE
				RegType_RegOptionPrice.regOptionPriceId = :id
			AND
				RegType_RegOptionPrice.regTypeId is NULL
			AND
				RegOptionPrice.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if reg option price is visible to all reg types.');

		return !empty($result);
	}
	
	/**
	 * 
	 * @param array $params [eventId , id]
	 */
	private function removeRegTypes($params) {
		$sql = '
			DELETE FROM
				RegType_RegOptionPrice
			WHERE
				RegType_RegOptionPrice.regOptionPriceId = :id
			AND
				RegType_RegOptionPrice.regOptionPriceId 
			IN (
				SELECT RegOptionPrice.id
				FROM RegOptionPrice
				WHERE RegOptionPrice.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Remove all reg types from reg option price visibility.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, priceId, regTypeIds]
	 */
	private function setRegTypes($params) {
		$this->checkRegOptionPricePermission(array(
			'eventId' => $params['eventId'],
			'id' => $params['priceId']
		));
		
		if(in_array(-1, $params['regTypeIds'])) {
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
				'regOptionPriceId' => $params['priceId']
			);
			
			$this->execute($sql, $params, 'Set reg option price visibile to reg types.');
		}
		else {
			foreach($params['regTypeIds'] as $regTypeId) {
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
					'regOptionPriceId' => $params['priceId']
				);
				
				$this->execute($sql, $params, 'Set reg option price visibile to reg types.');
			}
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, priceId, regOptionId]
	 */
	private function setRegOption($params) {
		$this->checkRegOptionPricePermission(array(
			'eventId' => $params['eventId'],
			'id' => $params['priceId']
		));
		
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
			'regOptionId' => $params['regOptionId'],
			'regOptionPriceId' => $params['priceId']
		);
		
		$this->execute($sql, $params, 'Create reg option/price association.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, optionId, priceId]
	 */
	private function setVariableQuantity($params) {
		$this->checkRegOptionPricePermission(array(
			'eventId' => $params['eventId'],
			'id' => $params['priceId']
		));
		
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
			'variableQuantityId' => $params['optionId'],
			'regOptionPriceId' => $params['priceId']
		);
		
		$this->execute($sql, $params, '');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	private function findVariableQuantityPrice($params) {
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
			AND
				RegOptionPrice.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find variable quantity option price.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	private function findRegOptionPrice($params) {
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
			AND
				RegOptionPrice.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find reg option price.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	private function checkRegOptionPricePermission($params) {
		$sql = '
			SELECT
				id,
				eventId
			FROM
				RegOptionPrice
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$results = $this->rawQuery($sql, $params, 'Check reg option price permission.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to access RegOptionPrice. (event id, price id) -> ({$params['eventId']}, {$params['id']}).");
		}
	}
}

?>