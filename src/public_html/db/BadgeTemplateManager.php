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
		$obj['appliesTo'] = db_RegTypeManager::getInstance()->findForBadgeTemplate($obj);
		$obj['cells'] = db_BadgeCellManager::getInstance()->findByBadgeTemplateId($obj['id']);
		
		return $obj;
	}
	
	public function find($id) {
		return $this->selectUnique(
			'BadgeTemplate', 
			array(
				'id',
				'eventId',
				'name',
				'type'
			), 
			array(
				'id' => $id
			)
		);
	}
	
	public function findByEventId($eventId) {
		return $this->select(
			'BadgeTemplate', 
			array(
				'id',
				'eventId',
				'name',
				'type'
			), 
			array(
				'eventId' => $eventId
			)
		);
	}
	
	public function findByRegTypeId($eventId, $regTypeId) {
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
		
		$params = array(
			'eventId' => $eventId,
			'regTypeId' => $regTypeId
		);
		
		return $this->query($sql, $params, 'Find badge template by reg type id.');
	}
	
	public function createBadgeTemplate($data) { 
		$this->insert(
			'BadgeTemplate', 
			ArrayUtil::keyIntersect($data, array('eventId', 'name', 'type'))
		);
		
		$newId = $this->lastInsertId();
		
		$this->setBadgeTemplateRegTypes($newId, $data['regTypeIds']);
		
		return $newId;
	}

	public function setBadgeTemplateRegTypes($id, $typeIds) {
		// remove existing reg types.
		$this->del('BadgeTemplate_RegType', array('badgeTemplateId' => $id));
		
		// -1 indicates that template applies to all reg types.
		if(in_array(-1, $typeIds)) {
			$this->insert(
				'BadgeTemplate_RegType', 
				array(
					'badgeTemplateId' => $id
				)
			);
		}
		else {
			foreach($typeIds as $typeId) {
				$this->insert(
					'BadgeTemplate_RegType', 
					array(
						'badgeTemplateId' => $id,
						'regTypeId' => $typeId	
					)
				);
			}
		}
	}
	
	private function isAppliedToAll($t) {
		$sql = '
			SELECT
				badgeTemplateId
			FROM
				BadgeTemplate_RegType
			WHERE
				badgeTemplateId = :badgeTemplateId
			AND
				regTypeId is NULL
		';
		
		$params = array('badgeTemplateId' => $t['id']);
		
		$r = $this->rawQueryUnique($sql, $params, 'Check if badge template applies to all reg types.');
		
		return !empty($r);
	}
	
	public function save($template) { 
		$this->update(
			'BadgeTemplate', 
			array(
				'name' => $template['name'],
				'type' => $template['type']
			), 
			array('id' => $template['id'])
		);
		
		$this->setBadgeTemplateRegTypes($template['id'], $template['regTypeIds']);
	}
	
	public function delete($id) {
		$this->del('BadgeTemplate_RegType', array('badgeTemplateId' => $id));
		db_BadgeCellManager::getInstance()->deleteByTemplateId($id);
		
		$this->del('BadgeTemplate', array('id' => $id));
	}
	
	public function findPrintBadgeTemplate($eventId, $regTypeId, $templateIds) {
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
		
		$params = array(
			'eventId' => $eventId,
			'regTypeId' => $regTypeId,
			'templateIds' => $templateIds
		);
		
		return $this->queryUnique($sql, $params, 'Find badge template from list.');
	}
	
	/**
	 * Delete reg type relations for the given badge templates.
	 * @param array $params ['eventId', 'templateIds'] 
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
	 * @param array $params ['eventId', 'templateIds'] 
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
}

?>