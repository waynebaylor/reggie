<?php

class db_RegTypeManager extends db_OrderableManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'RegType';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		$obj['visibleTo'] = db_CategoryManager::getInstance()->findByRegType($obj);
		return $obj; 
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_RegTypeManager();
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
				displayOrder
			FROM
				RegType
			WHERE
				id=:id
		';
		
		$params = array(
			':id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find reg type.');
	}
	
	public function createRegType($eventId, $sectionId, $description, $code, $categories) {
		$sql = '
			INSERT INTO
				RegType(
					eventId,
					sectionId,
					code,
					description,
					displayOrder
				)
			VALUES(
				:eventId,
				:sectionId,
				:code,
				:description,
				:displayOrder
			)
		';

		$params = array(
			'eventId' => $eventId,
			'sectionId' => $sectionId,
			'description' => $description,
			'code' => $code,
			'displayOrder' => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Create registration type.');
		
		// create the mapping rows.
		$regTypeId = $this->lastInsertId();
		$this->makeRegTypeAvailableTo($regTypeId, $categories);
	}
	
	public function findBySection($section) {
		$sql = '
			SELECT
				id,
				eventId,
				sectionId,
				code,
				description,
				displayOrder
			FROM
				RegType
			WHERE
				sectionId=:sectionId	
			ORDER BY
				displayOrder		
		';
		
		$params = array(
			'sectionId' => $section['id']
		);
		
		return $this->query($sql, $params, 'Find reg type by section.');
	}
	
	public function findByEvent($event) {
		$sql = '
			SELECT
				id,
				eventId,
				sectionId,
				code,
				description,
				displayOrder
			FROM
				RegType
			WHERE
				eventId=:eventId	
			ORDER BY
				displayOrder
		';
		
		$params = array(
			'eventId' => $event['id']
		);
		
		return $this->query($sql, $params, 'Find reg type by event.');
	}
	
	public function save($regType, $categoryIds) {
		$sql = '
			UPDATE
				RegType
			SET
				description=:description,
				code=:code
			WHERE
				id=:id
		';

		$params = array(
			'description' => $regType['description'],
			'code' => $regType['code'],
			'id' => $regType['id']
		);
		
		$this->execute($sql, $params, 'Save reg type.');
		
		// update category mappings.
		$this->makeRegTypeAvailableTo($regType['id'], $categoryIds);
	}
	
	public function delete($regType) {
		$sql = '
			DELETE FROM
				RegType
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $regType['id']
		);
		
		$this->execute($sql, $params, 'Delete registration type.');
	}
	
	public function moveRegTypeUp($regType) {
		$this->moveUp($regType, 'sectionId', $regType['sectionId']);
	}
	
	public function moveRegTypeDown($regType) {
		$this->moveDown($regType, 'sectionId', $regType['sectionId']);
	}
	
	private function makeRegTypeAvailableTo($regTypeId, $categoryIds) {
		// remove existing mappings.
		$sql = '
			DELETE FROM
				CategoryRegType
			WHERE
				regTypeId=:regTypeId
		';
		
		$params = array(
			'regTypeId' => $regTypeId
		);
		
		$this->execute($sql, $params, 'Make reg type unavailable.');
		
		// add new mappings.
		$sql = '
			INSERT INTO
				CategoryRegType(
					categoryId,
					regTypeId	
				) 
			VALUES(
				:categoryId,
				:regTypeId
			)
		';
		
		foreach($categoryIds as $categoryId) {
			$params = array(
				'categoryId' => $categoryId,
				'regTypeId' => $regTypeId
			);
			
			$this->execute($sql, $params, 'Make reg type available to category.');
		}
	}
	
	public function findRegTypesForField($field) {
		// check if field is visible to all reg types.
		if(!$field['visibleToAll']) {
			$sql = '
				SELECT
					RegType.id,
					RegType.code,
					RegType.description
				FROM
					RegType
				INNER JOIN
					RegTypeContactField
				ON
					RegType.id=RegTypeContactField.regTypeId
				WHERE
					RegTypeContactField.contactFieldId=:id
			';
		
			$params = array(
				'id' => $field['id']
			);

			return $this->query($sql, $params, 'Find reg types for which contact field is visible.');
		}
		else {
			// have to use the field's sectionId to work our way 
			// up to the eventId since RegTypeContactField.regTypeId is NULL.
			$sql = '
				SELECT
					RegType.id,
					RegType.code,
					RegType.description
				FROM
					RegType
				INNER JOIN
					Event
				ON
					Event.id=RegType.eventId
				INNER JOIN
					Page
				ON
					Event.id=Page.eventId
				INNER JOIN
					Section
				ON
					Page.id=Section.pageId
				WHERE
					Section.id=:sectionId
			';
			
			$params = array(
				'sectionId' => $field['sectionId']
			);
			
			return $this->query($sql, $params, 'Contact field visible for all reg types. Find all event reg types.');	
		}	
	}
	
	public function findByPrice($price) {
		// check if price is visible to all reg types.
		if(!$price['visibleToAll']) {
			$sql = '
				SELECT
					RegType.id,
					RegType.code,
					RegType.description
				FROM
					RegType
				INNER JOIN
					RegType_RegOptionPrice
				ON
					RegType.id=RegType_RegOptionPrice.regTypeId
				WHERE
					RegType_RegOptionPrice.regOptionPriceId=:id
			';
			
			$params = array(
				'id' => $price['id']
			);

			return $this->query($sql, $params, 'Find reg types for which reg option price is visible.');
		}
		else {
			if(db_RegOptionPriceManager::getInstance()->isVariableQuantityPrice($price)) {
				$sql = '
					SELECT
						VariableQuantityOption.sectionId
					FROM
						VariableQuantityOption
					INNER JOIN
						VariableQuantityOption_RegOptionPrice
					ON
						VariableQuantityOption.id=VariableQuantityOption_RegOptionPrice.variableQuantityId
					WHERE
						VariableQuantityOption_RegOptionPrice.regOptionPriceId=:id
				';
				
				$params = array(
					'id' => $price['id']
				);
				
				$result = $this->rawQueryUnique($sql, $params, 'Find section associated with variable quantity price.');
				$sectionId = $result['sectionId'];
			}
			else {
				// get all reg types for the event since RegType_RegOptionPrice.regTypeId is NULL.
				$groupId = $this->getPriceGroupId($price);
					
				// walk up to a section level group.
				$sectionId = $this->getSectionId($groupId);
				while(empty($sectionId)) {
					$optionId = $this->getOptionId($groupId);
					$groupId = $this->getOptionGroupId($optionId);
					$sectionId = $this->getSectionId($groupId);
				}
			}
				
			$sql = '
				SELECT
					Page.eventId as eventId
				FROM
					Section
				INNER JOIN
					Page
				ON
					Section.pageId=Page.id
				WHERE
					Section.id=:id
			';
				
			$params = array(
				'id' => $sectionId
			);
				
			$result = $this->rawQueryUnique($sql, $params, 'Find event for section.');
			$event = array(
				'id' => $result['eventId']
			);
				
			return $this->findByEvent($event);
		}
	}
	
	private function getPriceGroupId($price) {
		$sql = '
				SELECT
					RegOptionGroup.id as groupId	
				FROM
					RegOptionPrice
				INNER JOIN
					RegOption_RegOptionPrice
				ON
					RegOptionPrice.id=RegOption_RegOptionPrice.regOptionPriceId
				INNER JOIN
					RegOption
				ON
					RegOption.id=RegOption_RegOptionPrice.regOptionId
				INNER JOIN
					RegOptionGroup
				ON
					RegOption.parentGroupId=RegOptionGroup.id
				WHERE
					RegOptionPrice.id=:id
					
			';
			
			$params = array(
				'id' => $price['id']
			);
			
			$result = $this->rawQueryUnique($sql, $params, 'Find reg option group for price.');
			return $result['groupId'];
	}
	
	private function getSectionId($groupId) {
		$sql = '
			SELECT
				sectionId
			FROM
				Section_RegOptionGroup
			WHERE
				optionGroupId=:id
		';
		
		$params = array(
			'id' => $groupId
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if group is a child of a section.');
		
		return $result['sectionId'];
	}
	
	private function getOptionId($groupId) {
		$sql = '
			SELECT
				regOptionId
			FROM
				RegOption_RegOptionGroup
			WHERE
				optionGroupId=:id
		';
		
		$params = array(
			'id' => $groupId
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Check is group is a child of an option.');
		
		return $result['regOptionId'];
	}
	
	private function getOptionGroupId($optionId) {
		$sql = '
			SELECT
				parentGroupId
			FROM
				RegOption
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $optionId
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Get group id for option.');
		
		return $result['parentGroupId'];
	}
}

?>