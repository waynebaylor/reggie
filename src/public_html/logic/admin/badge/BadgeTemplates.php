<?php

class logic_admin_badge_BadgeTemplates extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		return array(
			'eventId' => $eventInfo['id'],
			'eventCode' => $eventInfo['code'],
			'templates' => db_BadgeTemplateManager::getInstance()->findByEventId($eventInfo['id'])
		);
	}
	
	public function addTemplate($params) {
		$values = ArrayUtil::keyIntersect($params, array('eventId', 'name', 'regTypeIds'));
		$values['type'] = $params['badgeTemplateType'];
		
		db_BadgeTemplateManager::getInstance()->createBadgeTemplate($values);
		
		return $this->view(array(
			'eventId' => $params['eventId']
		));
	}
	
	public function removeTemplate($params) {
		$template = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['id']);
		db_BadgeTemplateManager::getInstance()->delete($template['id']);
		
		return $this->view(array(
			'eventId' => $template['eventId']
		));
	}
	
	public function copyTemplate($params) {
		// copy badge template.
		$template = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['id']);
		$copyTemplateId = $this->copyBadgeTemplate($template);
		
		return $this->view(array(
			'eventId' => $template['eventId']
		));
	}
	
	private function copyBadgeTemplate($template) {
		// pull out the reg type ids.
		$regTypeIds = array();
		if($template['appliesToAll']) {
			$regTypeIds[] = -1;
		}
		else {
			foreach($template['appliesTo'] as $regType) {
				$regTypeIds[] = $regType['id'];
			}
		}
		
		// create copy of template.
		$copyId = db_BadgeTemplateManager::getInstance()->createBadgeTemplate(array(
			'eventId' => $template['eventId'],
			'name' => 'Copy of '.$template['name'],
			'type' => $template['type'],
			'regTypeIds' => $regTypeIds
		));
		
		// copy badge cells.
		foreach($template['cells'] as $cell) {
			$cell['badgeTemplateId'] = $copyId;
			$this->copyBadgeCell($cell);
		}	
	}
	
	private function copyBadgeCell($cell) {
		$copyId = db_BadgeCellManager::getInstance()->createBadgeCell($cell);

		// copy cell text and contact info fields. 
		foreach($cell['content'] as $cellContent) {
			$cellContent['badgeCellId'] = $copyId;
			
			if(empty($cellContent['contactFieldId'])) { 
				db_BadgeCellManager::getInstance()->addText($cellContent);
			}
			else {
				db_BadgeCellManager::getInstance()->addInformationField($cellContent);
			}
		}
		
		// copy barcode fields. 
		foreach($cell['barcodeFields'] as $barcodeField) {
			$barcodeField['badgeCellId'] = $copyId;
			
			db_BadgeBarcodeFieldManager::getInstance()->addInformationField($barcodeField);
		}
	}
}

?>