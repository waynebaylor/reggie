<?php

class logic_admin_badge_EditBadgeTemplate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) { 
		$badgeTemplate = db_BadgeTemplateManager::getInstance()->findTemplate(ArrayUtil::keyIntersect($params, array('eventId', 'id')));
		$selectedCell = $this->getSelectedCell($badgeTemplate, $params['selectedCellId']);
		$badgeCells = page_admin_badge_Helper::badgeCellSummaries($badgeTemplate, $selectedCell['id']);
		$event = db_EventManager::getInstance()->find($badgeTemplate['eventId']);
		
		$appliesToIds = array();
		if($badgeTemplate['appliesToAll']) {
			$appliesToIds[] = -1;
		}
		else {
			foreach($badgeTemplate['appliesTo'] as $regType) {
				$appliesToIds[] = $regType['id'];
			}
		}
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'template' => $badgeTemplate,
			'eventId' => $event['id'],
			'eventCode' => $event['code'],
			'event' => $event,
			'badgeCells' => $badgeCells,
			'appliesToRegTypeIds' => $appliesToIds,
			'selectedCell' => $selectedCell,
			'templateType' => model_BadgeTemplateType::newTemplate($badgeTemplate['type'])
		);
	}
	
	public function addBadgeCell($params) {
		$badgeTemplate = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['badgeTemplateId']);
		
		$newCellId = db_BadgeCellManager::getInstance()->createBadgeCell(array(
			'eventId' => $badgeTemplate['eventId'],
			'badgeTemplateId' => $badgeTemplate['id'],
			'xCoord' => 0, // inches
			'yCoord' => 0, // inches
			'width' => ($params['contentType'] === 'barcode')? badgeTemplateType_BaseTemplate::$BARCODE_WIDTH : 4, // inches
			'font' => 'arial',
			'fontSize' => 12, // pt
			'horizontalAlign' => 'C',
			'hasBarcode' => ($params['contentType'] === 'barcode')? 'T' : 'F'
		));
		
		if($params['contentType'] === 'field') {
			db_BadgeCellManager::getInstance()->addInformationField(array(
				'eventId' => $badgeTemplate['eventId'],
				'badgeCellId' => $newCellId,
				'templateField' => $params['templateField']
			));
		}
		else if($params['contentType'] === 'text') {
			db_BadgeCellManager::getInstance()->addText(array(
				'eventId' => $badgeTemplate['eventId'],
				'badgeCellId' => $newCellId,
				'text' => $params['text']
			));
		}
		
		return $this->view(array(
			'id' => $badgeTemplate['id'],
			'eventId' => $badgeTemplate['eventId'],
			'selectedCellId' => 0
		));
	}
	
	public function saveTemplate($params) {
		$badgeTemplate = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['id']);
		db_BadgeTemplateManager::getInstance()->save($params);
		
		return array();
	}
	
	private function getSelectedCell($template, $cellId) {
		$selectedCell = null;
		
		foreach($template['cells'] as $index => $cell) {
			if($index === 0 || intval($cell['id']) === intval($cellId)) {
				$selectedCell = $cell;
			}
		}
		
		return $selectedCell;
	}
	
	public function saveCellDetails($params) {
		$cell = db_BadgeCellManager::getInstance()->find($params);
		$badgeTemplate = db_BadgeTemplateManager::getInstance()->find($cell['badgeTemplateId']);
	
		db_BadgeCellManager::getInstance()->saveBadgeCell($params);
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id'],
			'eventId' => $params['eventId']
		));
	}
	
	public function addCellContent($params) { 
		$cell = db_BadgeCellManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['cellId']
		));

		if($cell['hasBarcode'] === 'T') {
			db_BadgeBarcodeFieldManager::getInstance()->addInformationField(array(
				'eventId' => $params['eventId'],
				'badgeCellId' => $cell['id'],
				'contactFieldId' => $params['contactFieldId']
			));
		}
		else {
			if($params['contentType'] === 'text') {
				db_BadgeCellManager::getInstance()->addText(array(
					'eventId' => $params['eventId'],
					'badgeCellId' => $cell['id'],
					'text' => $params['text']
				));
			} 	
			else if($params['contentType'] === 'field') {
				db_BadgeCellManager::getInstance()->addInformationField(array(
					'eventId' => $params['eventId'],
					'badgeCellId' => $cell['id'],
					'templateField' => $params['templateField']
				));
			}	
		}
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id'],
			'eventId' => $params['eventId']
		));
	}
	
	public function moveCellContentUp($params) {
		$cellContent = db_BadgeCellManager::getInstance()->findBadgeCellContentById($params['id']);
		$cellContent['eventId'] = $params['eventId'];
		
		db_BadgeCellManager::getInstance()->moveCellContentUp($cellContent);
		
		$cell = db_BadgeCellManager::getInstance()->find(array(
			'eventId' => $params['eventId'], 
			'id' => $cellContent['badgeCellId']
		));
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id'],
			'eventId' => $params['eventId']
		));
	}
	
	public function moveCellContentDown($params) {
		$cellContent = db_BadgeCellManager::getInstance()->findBadgeCellContentById($params['id']);
		$cellContent['eventId'] = $params['eventId'];
		
		db_BadgeCellManager::getInstance()->moveCellContentDown($cellContent);
		
		$cell = db_BadgeCellManager::getInstance()->find(array(
			'eventId' => $params['eventId'], 
			'id' => $cellContent['badgeCellId']
		));
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id'],
			'eventId' => $params['eventId']
		));
	}
	
	public function removeCellContent($params) {
		$cell = db_BadgeCellManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['cellId']
		));
		
		if($cell['hasBarcode'] === 'T') {
			db_BadgeBarcodeFieldManager::getInstance()->deleteBadgeBarcodeField(array(
				'eventId' => $params['eventId'],
				'id' => $params['cellId']
			));	
		}
		else {
			db_BadgeCellManager::getInstance()->deleteBadgeCellContent($params);
		}
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id'],
			'eventId' => $params['eventId']
		));
	}
	
	public function removeBadgeCell($params) {
		$cell = db_BadgeCellManager::getInstance()->find($params);
		db_BadgeCellManager::getInstance()->deleteBadgeCell($params);
		
		$badgeTemplate = db_BadgeTemplateManager::getInstance()->find($cell['badgeTemplateId']);
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => 0,
			'eventId' => $badgeTemplate['eventId']
		));
	}
}

?>