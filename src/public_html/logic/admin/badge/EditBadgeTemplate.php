<?php

class logic_admin_badge_EditBadgeTemplate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$badgeTemplate = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['id']);
		$selectedCell = $this->getSelectedCell($badgeTemplate, $params['selectedCellId']);
		$badgeCells = page_admin_badge_Helper::badgeCellSummaries($badgeTemplate, $selectedCell['id']);
		$eventInfo = db_EventManager::getInstance()->findInfoById($badgeTemplate['eventId']);
		
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
			'template' => $badgeTemplate,
			'eventId' => $eventInfo['id'],
			'eventCode' => $eventInfo['code'],
			'badgeCells' => $badgeCells,
			'appliesToRegTypeIds' => $appliesToIds,
			'selectedCell' => $selectedCell,
			'templateType' => new badgeTemplateType_ThreeByFourDouble()
		);
	}
	
	public function addBadgeCell($params) {
		$badgeTemplate = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['badgeTemplateId']);
		
		$newCellId = db_BadgeCellManager::getInstance()->createBadgeCell(array(
			'badgeTemplateId' => $badgeTemplate['id'],
			'xCoord' => 0, // inches
			'yCoord' => 0, // inches
			'width' => 4, // inches
			'font' => 'helvetica',
			'fontSize' => 12, // pt
			'horizontalAlign' => 'C',
			'hasBarcode' => ($params['contentType'] === 'barcode')? 'T' : 'F'
		));
		
		if($params['contentType'] === 'field') {
			db_BadgeCellManager::getInstance()->addInformationField(array(
				'badgeCellId' => $newCellId,
				'contactFieldId' => $params['contactFieldId']
			));
		}
		else if($params['contentType'] === 'text') {
			db_BadgeCellManager::getInstance()->addText(array(
				'badgeCellId' => $newCellId,
				'text' => $params['text']
			));
		}
		else if($params['contentType'] === 'barcode') {
			// TODO add barcode
		}
		
		return $this->view(array(
			'id' => $badgeTemplate['id'],
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
			if($index === 0 || $cell['id'] === $cellId) {
				$selectedCell = $cell;
			}
		}
		
		return $selectedCell;
	}
	
	public function saveCellDetails($params) {
		$cell = $this->strictFindById(db_BadgeCellManager::getInstance(), $params['id']);
		db_BadgeCellManager::getInstance()->saveBadgeCell($params);
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id']
		));
	}
	
	public function addCellContent($params) {
		$cell = $this->strictFindById(db_BadgeCellManager::getInstance(), $params['cellId']);
		
		if($params['contentType'] === 'text') {
			db_BadgeCellManager::getInstance()->addText(array(
				'badgeCellId' => $cell['id'],
				'text' => $params['text']
			));
		} 	
		else if($params['contentType'] === 'field') {
			db_BadgeCellManager::getInstance()->addInformationField(array(
				'badgeCellId' => $cell['id'],
				'contactFieldId' => $params['contactFieldId']
			));
		}	
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id']
		));
	}
	
	public function moveCellContentUp($params) {
		$cellContent = db_BadgeCellManager::getInstance()->findBadgeCellContentById($params['id']);
		db_BadgeCellManager::getInstance()->moveCellContentUp($cellContent);
		
		$cell = $this->strictFindById(db_BadgeCellManager::getInstance(), $cellContent['badgeCellId']);
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id']
		));
	}
	
	public function moveCellContentDown($params) {
		$cellContent = db_BadgeCellManager::getInstance()->findBadgeCellContentById($params['id']);
		db_BadgeCellManager::getInstance()->moveCellContentDown($cellContent);
		
		$cell = $this->strictFindById(db_BadgeCellManager::getInstance(), $cellContent['badgeCellId']);
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id']
		));
	}
	
	public function removeCellContent($params) {
		$cell = $this->strictFindById(db_BadgeCellManager::getInstance(), $params['cellId']);
		
		db_BadgeCellManager::getInstance()->deleteBadgeCellContent($params['id']);
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => $cell['id']
		));
	}
	
	public function removeBadgeCell($params) {
		$cell = $this->strictFindById(db_BadgeCellManager::getInstance(), $params['id']);
		db_BadgeCellManager::getInstance()->deleteBadgeCell($cell['id']);
		
		return $this->view(array(
			'id' => $cell['badgeTemplateId'],
			'selectedCellId' => 0
		));
	}
}

?>