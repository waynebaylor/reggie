<?php

class logic_admin_badge_PrintBadge extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function singleBadge($params) {
		$badgeTemplate = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['badgeTemplateId']);
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		$user = SessionUtil::getUser();
		
		$data = $this->getRegistrationBadgeData($params['registrationId'], $badgeTemplate);
		
		return array(
			'badgeTemplate' => $badgeTemplate,
			'margins' => $params['margins'],
			'shiftRight' => $params['shiftRight'],
			'shiftDown' => $params['shiftDown'],
			'eventInfo' => $eventInfo,
			'user' => $user,
			'data' => $data
		);
	}
	
	public function allBadges($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		$regInfos = db_reg_RegistrationManager::getInstance()->findInfoOrderedByField($params['eventId'], $params['sortByFieldId'], $params['templateIds']);
		
		// get the reg infos for the requested batch.
		$BATCH_SIZE = 48; // divisible by 3 to fill full pages. testing shows this size can be done before timeout.
		if($params['batchNumber'] > 0) {
			$regInfos = array_slice($regInfos, $BATCH_SIZE*($params['batchNumber']-1), $BATCH_SIZE);
		}
		
		$allData = array();
		
		foreach($regInfos as $regInfo) { 
			$badgeTemplate = db_BadgeTemplateManager::getInstance()->findPrintBadgeTemplate($regInfo['eventId'], $regInfo['regTypeId'], $params['templateIds']);
			
			if(!empty($badgeTemplate)) {
				$regData = $this->getRegistrationBadgeData($regInfo['id'], $badgeTemplate);
				
				$allData[] = array(
					'template' => $badgeTemplate,
					'data' => $regData
				);
			}
		}
		
		return array(
			'user' => SessionUtil::getUser(),
			'eventInfo' => $eventInfo,
			'data' => $allData,
			'batchNumber' => $params['batchNumber']
		);
	}
	
	private function getCellText($registrationId, $cell) {
		$text = '';
		
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $registrationId);
		
		foreach($cell['content'] as $subCell) {
			if($subCell['showRegType'] === 'T') {
				$regType = db_RegTypeManager::getInstance()->find($registration['regTypeId']);
				$text .= $regType['description'];
			}
			else if($subCell['showLeadNumber'] === 'T') {
				$text .= model_Registrant::getLeadNumber($registration);
			}
			else if(empty($subCell['contactFieldId'])) {
				$text .= $subCell['text'];
			}
			else {
				// get registrant value for contact field.
				$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $subCell['contactFieldId']);
				$value = model_Registrant::getInformationValue($registration, $field);
			
				// option fields may have multiple values selected.
				if(model_FormInput::isOptionInput($field['formInput']['id'])) {
					$value = $this->getOptionFieldValue($field, $value);
				}
				
				$text .= $value;
			}
		}
		
		return $text;
	}
	
	private function getBarcodeText($registrationId, $cell) {
		$text = '';
		
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $registrationId);
		
		foreach($cell['barcodeFields'] as $barcodeField) {
			$field = $this->strictFindById(db_ContactFieldManager::getInstance(), $barcodeField['contactFieldId']);
			$value = model_Registrant::getInformationValue($registration, $field);
			
			// option fields may have multiple values selected.
			if(model_FormInput::isOptionInput($field['formInput']['id'])) {
				$value = $this->getOptionFieldValue($field, $value);
			}
			
			$text .= $value.'\r\n';
		}
 		
		return $text;
	}
	
	private function getRegistrationBadgeData($registrationId, $badgeTemplate) {
		$data = array();
		
		foreach($badgeTemplate['cells'] as $cell) {
			if($cell['hasBarcode'] === 'T') {
				$data[] = array(
					'isBarcode' => true,
					'xCoord' => $cell['xCoord'],
					'yCoord' => $cell['yCoord'],
					'text' => $this->getBarcodeText($registrationId, $cell),
					'align' => $cell['horizontalAlign']
				);
			}
			else {
				$data[] = array(
					'isBarcode' => false,
					'font' => $cell['font'],
					'fontSize' => $cell['fontSize'],
					'xCoord' => $cell['xCoord'],
					'yCoord' => $cell['yCoord'],
					'width' => $cell['width'],
					'text' => $this->getCellText($registrationId, $cell),
					'align' => $cell['horizontalAlign']
				);
			}
		}

		return $data;
	}
}

?>