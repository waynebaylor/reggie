<?php

class logic_admin_badge_EditBadgeTemplate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$badgeTemplate = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['id']);
		$badgeCells = $this->badgeCellSummaries($badgeTemplate);
		$eventInfo = db_EventManager::getInstance()->findInfoById($badgeTemplate['eventId']);
		
		return array(
			'template' => $badgeTemplate,
			'eventId' => $eventInfo['id'],
			'eventCode' => $eventInfo['code'],
			'badgeCells' => $badgeCells
		);
	}
	
	private function badgeCellSummaries($template) {
		$summaries = array();
		
		foreach($template['cells'] as $cell) {
			$summary = '';
			
			if($cell['hasBarcode'] === 'T') {
				$summary .= 'Barcode';
			}
			else {
				foreach($cell['content'] as $content) {
					if(empty($content['contactFieldId'])) {
						$summary .= $content['text'];
					}
					else {
						$field = db_ContactFieldManager::getInstance()->find($content['contactFieldId']);
						$summary .= "<{$field['displayName']}>";
					}
				}
			}
			
			$summaries[] = $summary;
		}
		
		return $summaries;
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
			'id' => $badgeTemplate['id']
		));
	}
}

?>