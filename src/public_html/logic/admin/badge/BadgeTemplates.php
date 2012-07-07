<?php

class logic_admin_badge_BadgeTemplates extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $event['id'],
			'event' => $event,
			'templates' => db_BadgeTemplateManager::getInstance()->findByEventId(array('eventId' => $event['id'])),
			'actionMenuEventLabel' => $event['code']
		);	
	}
	
	public function listTemplates($params) {
		return array(
			'eventId' => $params['eventId'],
			'templates' => db_BadgeTemplateManager::getInstance()->findByEventId($params)
		);
	}
	
	public function deleteTemplates($params) {
		db_BadgeTemplateManager::getInstance()->deleteTemplates($params);
		
		return array('eventId' => $params['eventId']);
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
		db_BadgeTemplateManager::getInstance()->delete(array(
			'eventId' => $params['eventId'],
			'badgeTemplateId' => $params['id']
		));
		
		return $this->view($params);
	}
	
	public function copyTemplate($params) { 
		// copy badge template.
		$template = db_BadgeTemplateManager::getInstance()->find($params);
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
			$cell['eventId'] = $template['eventId'];
			$this->copyBadgeCell($cell);
		}	
	}
	
	private function copyBadgeCell($params) {
		$cell = $params;
		
		$copyId = db_BadgeCellManager::getInstance()->createBadgeCell($cell);

		// copy cell text and contact info fields. 
		foreach($cell['content'] as $cellContent) {
			$cellContent['badgeCellId'] = $copyId;
			$cellContent['eventId'] = $params['eventId'];
			
			
			if(!empty($cellContent['text'])) { 
				db_BadgeCellManager::getInstance()->addText($cellContent);
			}
			else { 
				if($cellContent['showRegType'] === 'T') {
					$cellContent['templateField'] = 'registration_type';
				}
				else if($cellContent['showLeadNumber'] === 'T') {
					$cellContent['templateField'] = 'lead_number';
				}
				else {
					$cellContent['templateField'] = $cellContent['contactFieldId'];
				}
				
				db_BadgeCellManager::getInstance()->addInformationField($cellContent);
			}
		}
		
		// copy barcode fields. 
		foreach($cell['barcodeFields'] as $barcodeField) {
			$barcodeField['eventId'] = $params['eventId'];
			$barcodeField['badgeCellId'] = $copyId;
			
			db_BadgeBarcodeFieldManager::getInstance()->addInformationField($barcodeField);
		}
	}
}

?>