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
		
		return $obj;
	}
	
	public function find($id) {
		return $this->selectUnique(
			'BadgeTemplate', 
			array(
				'id',
				'eventId',
				'name'
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
				'name'
			), 
			array(
				'eventId' => $eventId
			)
		);
	}
	
	public function createBadgeTemplate($data) { 
		$this->insert(
			'BadgeTemplate', 
			ArrayUtil::keyIntersect($data, array('eventId', 'name'))
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
}

?>