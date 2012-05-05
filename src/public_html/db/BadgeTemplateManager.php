<?php

class db_BadgeTemplateManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_BadgeTemplateManager();
		}
		
		return self::$instance;
	}
	
	protected function populate(&$obj, $arr) { 
		parent::populate($obj, $arr);
		
		$obj['appliesToAll'] = $this->isAppliedToAll($obj); 
		$obj['appliesTo'] = db_RegTypeManager::getInstance()->findForBadgeTemplate(array(
			'eventId' => $obj['eventId'],
			'badgeTemplateId' => $obj['id'],
			'appliesToAll' => $obj['appliesToAll']
		));
		$obj['cells'] = db_BadgeCellManager::getInstance()->findByBadgeTemplateId(array(
			'eventId' => $obj['eventId'],
			'badgeTemplateId' => $obj['id']
		));
		
		return $obj;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
		return $this->selectUnique(
			'BadgeTemplate', 
			array(
				'id',
				'eventId',
				'name',
				'type'
			), 
			array(
				'id' => $params['id'],
				'eventId' => $params['eventId']
			)
		);
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEventId($params) { 
		return $this->select(
			'BadgeTemplate', 
			array(
				'id',
				'eventId',
				'name',
				'type'
			), 
			array(
				'eventId' => $params['eventId']
			)
		);
	}
	
	/**
	 * 
	 * @param array $params [eventId, regTypeId]
	 */
	public function findByRegTypeId($params) {
		$sql = '
			SELECT
				BadgeTemplate.id,
				BadgeTemplate.eventId,
				BadgeTemplate.name,
				BadgeTemplate.type
			FROM
				BadgeTemplate
			INNER JOIN
				BadgeTemplate_RegType
			ON
				BadgeTemplate.id = BadgeTemplate_RegType.badgeTemplateId
			WHERE
				BadgeTemplate.eventId = :eventId
			AND (
				BadgeTemplate_RegType.regTypeId is NULL
				OR
				BadgeTemplate_RegType.regTypeId = :regTypeId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId', 
			'regTypeId'
		));
		
		return $this->query($sql, $params, 'Find badge template by reg type id.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, name, type, regTypeIds]
	 */
	public function createBadgeTemplate($params) { 
		$this->insert(
			'BadgeTemplate', 
			ArrayUtil::keyIntersect($params, array('eventId', 'name', 'type'))
		);
		
		$newId = $this->lastInsertId();
		
		$this->setBadgeTemplateRegTypes(array(
			'eventId' => $params['eventId'],
			'badgeTemplateId' => $newId,
			'regTypeIds' => $params['regTypeIds']
		));
		
		return $newId;
	}

	/**
	 * 
	 * @param array $params [eventId, badgeTemplateId, regTypeIds]
	 */
	private function setBadgeTemplateRegTypes($params) {
		// remove existing reg types.
		$sql = '
			DELETE FROM
				BadgeTemplate_RegType
			WHERE
				BadgeTemplate_RegType.badgeTemplateId = :badgeTemplateId
			AND
				BadgeTemplate_RegType.badgeTemplateId 
			IN (
				SELECT BadgeTemplate.id 
				FROM BadgeTemplate
				WHERE BadgeTemplate.eventId = :eventId
			)
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'badgeTemplateId'));
		
		$this->execute($sql, $p, 'Delete badge template reg types.');
		
		// -1 indicates that template applies to all reg types.
		if(in_array(-1, $params['regTypeIds'])) {
			$this->insert(
				'BadgeTemplate_RegType', 
				ArrayUtil::keyIntersect($params, array('badgeTemplateId'))
			);
		}
		else {
			foreach($params['regTypeIds'] as $typeId) {
				$this->insert(
					'BadgeTemplate_RegType', 
					array(
						'badgeTemplateId' => $params['badgeTemplateId'],
						'regTypeId' => $typeId	
					)
				);
			}
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	private function isAppliedToAll($params) {
		$sql = '
			SELECT
				BadgeTemplate_RegType.badgeTemplateId
			FROM
				BadgeTemplate_RegType
			INNER JOIN
				BadgeTemplate
			ON
				BadgeTemplate_RegType.badgeTemplateId = BadgeTemplate.id
			WHERE
				BadgeTemplate_RegType.badgeTemplateId = :id
			AND
				BadgeTemplate.eventId = :eventId
			AND
				BadgeTemplate_RegType.regTypeId is NULL
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId', 
			'id'
		));
		
		$r = $this->rawQueryUnique($sql, $params, 'Check if badge template applies to all reg types.');
		
		return !empty($r);
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, name, type, regTypeIds]
	 */
	public function save($params) { 
		$this->update(
			'BadgeTemplate', 
			array(
				'name' => $params['name'],
				'type' => $params['type']
			), 
			array(
				'id' => $params['id'],
				'eventId' => $params['eventId']
			)
		);
		
		$this->setBadgeTemplateRegTypes(array(
			'eventId' => $params['eventId'],
			'badgeTemplateId' => $params['id'],
			'regTypeIds' => $params['regTypeIds']
		));
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeTemplateId]
	 */
	public function delete($params) {
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'badgeTemplateId'));
		
		// delete badge template reg types.
		$this->deleteBadgeTemplateRegTypes(array(
			'eventId' => $params['eventId'],
			'templateIds' => array($params['badgeTemplateId'])
		));
		
		// delete badge template cells.
		db_BadgeCellManager::getInstance()->deleteByTemplateId($params);
		
		// delete badge template.
		$this->del(
			'BadgeTemplate', 
			array(
				'eventId' => $params['eventId'],
				'id' => $params['badgeTemplateId']	
			)
		);
		
		$this->execute($sql, $params, 'Delete badge template.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, regTypeId, templateIds]
	 */
	public function findPrintBadgeTemplate($params) {
		$sql = '
			SELECT
				BadgeTemplate.id,
				BadgeTemplate.eventId,
				BadgeTemplate.name,
				BadgeTemplate.type
			FROM
				BadgeTemplate
			INNER JOIN
				BadgeTemplate_RegType
			ON
				BadgeTemplate.id = BadgeTemplate_RegType.badgeTemplateId
			WHERE
				BadgeTemplate.eventId = :eventId
			AND (
				BadgeTemplate_RegType.regTypeId IS NULL
			OR
				BadgeTemplate_RegType.regTypeId = :regTypeId
			)
			AND
				BadgeTemplate.id IN (:[templateIds])
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'regTypeId',
			'templateIds'
		));
		
		return $this->queryUnique($sql, $params, 'Find badge template from list.');
	}
	
	/**
	 * Delete reg type relations for the given badge templates.
	 * @param array $params [eventId, templateIds] 
	 */
	private function deleteBadgeTemplateRegTypes($params) {
		$sql = '
			DELETE FROM
				BadgeTemplate_RegType
			WHERE
				BadgeTemplate_RegType.badgeTemplateId IN (
					SELECT 
						BadgeTemplate.id
					FROM
						BadgeTemplate
					WHERE
						BadgeTemplate.eventId = :eventId
					AND
						BadgeTemplate.id IN (:[templateIds])
				)
		';
		
		$this->execute($sql, $params, 'Delete badge template reg types.');
	}
	
	/**
	 * Delete the given badge templates.
	 * @param array $params [eventId, templateIds] 
	 */
	public function deleteTemplates($params) {
		$this->deleteBadgeTemplateRegTypes($params);	
		
		db_BadgeCellManager::getInstance()->deleteBadgeCellsByTemplate($params);
		
		$sql = '
			DELETE FROM
				BadgeTemplate
			WHERE
				eventId = :eventId
			AND
				id IN (:[templateIds])
		';
		
		$this->execute($sql, $params, 'Delete badge templates.');
	}
	
	/**
	 * Find badge template.
	 * @param array $params [eventId, id]
	 */
	public function findTemplate($params) {
		$sql = '
			SELECT
				id,
				eventId,
				name,
				type
			FROM
				BadgeTemplate
			WHERE
				eventId = :eventId
			AND
				id = :id
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find badge template.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$sql = '
			SELECT
				id
			FROM
				BadgeTemplate
			WHERE
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		$results = $this->rawQuery($sql, $params, 'Find badge template ids.');
		
		$templateIds = array();
		foreach($results as $result) {
			$templateIds[] = $result['id'];
		}
		
		if(!empty($templateIds)) {
			$this->deleteTemplates(array('eventId' => $params['eventId'], 'templateIds' => $templateIds));
		}
	}
}

?>