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
				displayOrder
			FROM
				RegType
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find reg type.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, sectionId, code, description, categoryIds]
	 */
	public function createRegType($params) { 
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

		$p = ArrayUtil::keyIntersect($params, array('eventId', 'sectionId', 'code', 'description'));
		$p['displayOrder'] = $this->getNextOrder();
		
		$this->execute($sql, $p, 'Create registration type.');
		
		// create the mapping rows.
		$regTypeId = $this->lastInsertId();
		$this->makeRegTypeAvailableTo(array(
			'eventId' => $params['eventId'], 
			'regTypeId' => $regTypeId, 
			'categoryIds' => $params['categoryIds']
		));
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
				displayOrder
			FROM
				RegType
			WHERE
				sectionId = :sectionId
			AND
				eventId = :eventId	
			ORDER BY
				displayOrder		
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'sectionId'));
		
		return $this->query($sql, $params, 'Find reg type by section.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEvent($params) {
		return $this->findByEventId($params);	
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEventId($params) {
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
				eventId = :eventId	
			ORDER BY
				displayOrder
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->query($sql, $params, 'Find reg type by event.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, code, description, categoryIds]
	 */
	public function save($params) {
		$sql = '
			UPDATE
				RegType
			SET
				description = :description,
				code = :code
			WHERE
				id = :id
			AND
				eventId = :eventId
		';

		$p = ArrayUtil::keyIntersect($params, array('eventId', 'id', 'code', 'description'));
		
		$this->execute($sql, $p, 'Save reg type.');
		
		// update category mappings.
		$this->makeRegTypeAvailableTo(array(
			'eventId' => $params['eventId'],
			'regTypeId' => $params['id'],
			'categoryIds' => $params['categoryIds']
		));
	}
	
	/**
	 * 
	 * @param array $params [eventId, regTypeId]
	 */
	public function delete($params) {
		/////////////////////////////////////////////////////////////////////////////////
		// delete information field associations.
		$sql = '
			DELETE FROM
				RegTypeContactField
			WHERE
				RegTypeContactField.regTypeId = :regTypeId
			AND
				RegTypeContactField.regTypeId
			IN (
				SELECT RegType.id 
				FROM RegType
				WHERE RegType.eventId = :eventId
			)
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'regTypeId'));
		
		$this->execute($sql, $p, 'Delete information field associations.');
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete email template associations.
		$sql = '
			DELETE FROM
				RegType_EmailTemplate
			WHERE
				RegType_EmailTemplate.regTypeId = :regTypeId
			AND
				RegType_EmailTemplate.regTypeId 
			IN (
				SELECT RegType.id
				FROM RegType
				WHERE RegType.eventId = :eventId
			)
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'regTypeId'));
		
		$this->execute($sql, $p, 'Delete email template associations.');
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete reg option price associations.
		$sql = '
			DELETE FROM
				RegType_RegOptionPrice
			WHERE
				RegType_RegOptionPrice.regTypeId = :regTypeId
			AND
				RegType_RegOptionPrice.regTypeId
			IN (
				SELECT RegType.id
				FROM RegType
				WHERE RegType.eventId = :eventId
			)
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'regTypeId'));
		
		$this->execute($sql, $p, 'Delete reg option price associations.');
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete category associations.
		$this->removeRegTypeCategories($params);
		
		/////////////////////////////////////////////////////////////////////////////////
		// delete reg type.
		$sql = '
			DELETE FROM
				RegType
			WHERE
				id = :regTypeId
			AND
				eventId = :eventId
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'regTypeId'));
		
		$this->execute($sql, $params, 'Delete registration type.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function moveRegTypeUp($params) {
		$regType = $this->find($params);
		$this->moveUp($regType, 'sectionId', $regType['sectionId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function moveRegTypeDown($params) {
		$regType = $this->find($params);
		$this->moveDown($regType, 'sectionId', $regType['sectionId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, regTypeId]
	 */
	private function removeRegTypeCategories($params) {
		$sql = '
			DELETE FROM
				CategoryRegType
			WHERE
				CategoryRegType.regTypeId = :regTypeId
			AND
				CategoryRegType.regTypeId 
			IN (
				SELECT RegType.id
				FROM RegType
				WHERE RegType.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'regTypeId'));
		
		$this->execute($sql, $params, 'Make reg type unavailable.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, regTypeId, categoryIds]
	 */
	private function makeRegTypeAvailableTo($params) { 
		$this->checkRegTypePermission($params);
		
		// remove existing mappings.
		$this->removeRegTypeCategories($params);
		
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
		
		foreach($params['categoryIds'] as $categoryId) {
			$p = array(
				'categoryId' => $categoryId,
				'regTypeId' => $params['regTypeId']
			);
			
			$this->execute($sql, $p, 'Make reg type available to category.');
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, contactFieldId, visibleToAll, sectionId]
	 */
	public function findRegTypesForField($params) {
		// check if field is visible to all reg types.
		if(!$params['visibleToAll']) {
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
					RegType.id = RegTypeContactField.regTypeId
				WHERE
					RegTypeContactField.contactFieldId = :contactFieldId
				AND
					RegType.eventId = :eventId
			';
		
			$p = ArrayUtil::keyIntersect($params, array('eventId', 'contactFieldId'));

			return $this->query($sql, $p, 'Find reg types for which contact field is visible.');
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
					Event.id = RegType.eventId
				INNER JOIN
					Page
				ON
					Event.id = Page.eventId
				INNER JOIN
					Section
				ON
					Page.id = Section.pageId
				WHERE
					Section.id = :sectionId
				AND
					RegType.eventId = :eventId
			';
			
			$p = ArrayUtil::keyIntersect($params, array('eventId', 'sectionId'));
			
			return $this->query($sql, $p, 'Contact field visible for all reg types. Find all event reg types.');	
		}	
	}
	
	/**
	 * 
	 * @param array $params [eventId, priceId, visibleToAll]
	 */
	public function findByPrice($params) {
		// check if price is visible to all reg types.
		if(!$params['visibleToAll']) {
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
					RegType.id = RegType_RegOptionPrice.regTypeId
				WHERE
					RegType_RegOptionPrice.regOptionPriceId = :priceId
				AND
					RegType.eventId = :eventId
			';
			
			$params = ArrayUtil::keyIntersect($params, array('eventId', 'priceId'));

			return $this->query($sql, $params, 'Find reg types for which reg option price is visible.');
		}
		else {
			return $this->findByEventId($params);
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, priceId]
	 */
	private function getPriceGroupId($params) {
		$sql = '
				SELECT
					RegOptionGroup.id as groupId	
				FROM
					RegOptionPrice
				INNER JOIN
					RegOption_RegOptionPrice
				ON
					RegOptionPrice.id = RegOption_RegOptionPrice.regOptionPriceId
				INNER JOIN
					RegOption
				ON
					RegOption.id = RegOption_RegOptionPrice.regOptionId
				INNER JOIN
					RegOptionGroup
				ON
					RegOption.parentGroupId = RegOptionGroup.id
				WHERE
					RegOptionPrice.id = :priceId
				AND
					RegOption.eventId = :eventId
					
			';
			
			$params = ArrayUtil::keyIntersect($params, array('eventId', 'priceId'));
			
			$result = $this->rawQueryUnique($sql, $params, 'Find reg option group for price.');
			
			return $result['groupId'];
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionGroupId]
	 */
	private function getSectionId($params) {
		$sql = '
			SELECT
				id,
				sectionId
			FROM
				RegOptionGroup
			WHERE
				id = :regOptionGroupId
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'regOptionGroupId'));
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if section option-group.');
		
		return $result['sectionId'];
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionGroupId]
	 */
	private function getOptionId($params) {
		$sql = '
			SELECT
				id,
				regOptionId
			FROM
				RegOptionGroup
			WHERE
				id = :regOptionGroupId
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'regOptionGroupId'));
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if reg option option-group.');
		
		return $result['regOptionId'];
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionId]
	 */
	private function getOptionGroupId($params) {
		$sql = '
			SELECT
				parentGroupId
			FROM
				RegOption
			WHERE
				id = :regOptionId
			ANd
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'regOptionId'));
		
		$result = $this->rawQueryUnique($sql, $params, 'Get group id for option.');
		
		return $result['parentGroupId'];
	}
	
	/**
	 * 
	 * @param array $params [eventId, emailTemplateId, availableToAll]
	 */
	public function findForEmailTemplate($params) {
		if($params['availableToAll']) {
			return $this->findByEventId($params);
		}
		else {
			$sql = '
				SELECT
					RegType.id,
					RegType.code,
					RegType.description
				FROM
					RegType
				INNER JOIN
					RegType_EmailTemplate
				ON
					RegType.id = RegType_EmailTemplate.regTypeId
				WHERE
					RegType_EmailTemplate.emailTemplateId = :emailTemplateId
				AND
					RegType.eventId = :eventId
			';
			
			$params = ArrayUtil::keyIntersect($params, array('eventId', 'emailTemplateId'));
			
			return $this->rawQuery($sql, $params, 'Find reg types for which email template is available.');
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$regTypes = $this->findByEventId($params);
		
		foreach($regTypes as $regType) {
			$this->delete(array(
				'eventId' => $params['eventId'],
				'regTypeId' => $regType['id']
			));
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeTemplateId, appliesToAll]
	 */
	public function findForBadgeTemplate($params) {
		if($params['appliesToAll']) {
			return $this->findByEventId($params);
		}
		else {
			$sql = '
				SELECT
					RegType.id,
					RegType.code,
					RegType.description
				FROM
					RegType
				INNER JOIN
					BadgeTemplate_RegType
				ON
					RegType.id = BadgeTemplate_RegType.regTypeId
				WHERE
					BadgeTemplate_RegType.badgeTemplateId = :badgeTemplateId
				AND
					RegType.eventId = :eventId
			';
			
			$params = ArrayUtil::keyIntersect($params, array('eventId', 'badgeTemplateId'));
			
			return $this->rawQuery($sql, $params, 'Find reg types for which badge template is applied.');
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, regTypeId]
	 */
	private function checkRegTypePermission($params) {
		$sql = '
			SELECT
				id,
				eventId
			FROM
				RegType
			WHERE
				id = :regTypeId
			AND
				eventId = :eventId
		';	
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'regTypeId'));
		
		$results = $this->rawQuery($sql, $params, 'Check reg type permission.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to modify RegType. (event id, reg type id) -> ({$params['eventId']}, {$params['regTypeId']}).");
		}
	}
}

?>