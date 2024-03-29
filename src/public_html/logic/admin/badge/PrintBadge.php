<?php

class logic_admin_badge_PrintBadge extends logic_Performer
{
	// divisible by 3 to fill full pages. testing shows this size can be done before timeout.
	public static $BATCH_SIZE = 24;
	
	function __construct() {
		parent::__construct();
	}
	
	public function singleBadge($params) {
		$badgeTemplate = db_BadgeTemplateManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['badgeTemplateId']
		));
		
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		$user = SessionUtil::getUser();
		
		$data = $this->getRegistrationBadgeData($params['registrationId'], $badgeTemplate);
		
		return array(
			'badgeTemplate' => $badgeTemplate,
			'shiftRight' => $params['shiftRight'],
			'shiftDown' => $params['shiftDown'],
			'eventInfo' => $eventInfo,
			'user' => $user,
			'data' => $data
		);
	}
	
	public function allBadges($params) { 
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']); 
		$regInfos = db_reg_RegistrationManager::getInstance()->findInfoOrderedByField(
			$params['eventId'], $params['sortByFieldId'], $params['templateIds'], $params['startDate'], $params['endDate']);
		
		// get the reg infos for the requested batch.
		if($params['batchNumber'] >= 0) {
			$start = self::$BATCH_SIZE*($params['batchNumber']);
			$count = self::$BATCH_SIZE;
			
			$regInfos = array_slice($regInfos, $start, $count);
		}    
		
		$allData = array();
		
		foreach($regInfos as $regInfo) { 
			$badgeTemplate = db_BadgeTemplateManager::getInstance()->findPrintBadgeTemplate(array(
				'eventId' => $regInfo['eventId'], 
				'regTypeId' => $regInfo['regTypeId'], 
				'templateIds' => $params['templateIds']
			));
			
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
	
	public function batchCount($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		$regInfos = db_reg_RegistrationManager::getInstance()->findInfoOrderedByField(
			$params['eventId'], $params['sortByFieldId'], $params['templateIds'], $params['startDate'], $params['endDate']);

		$fullBatchCount = floor(count($regInfos)/self::$BATCH_SIZE);
		$partialBatch = (count($regInfos)%self::$BATCH_SIZE) > 0;
		
		return array(
			'eventId' => $params['eventId'],
			'sortByFieldId' => $params['sortByFieldId'],
			'templateIds' => $params['templateIds'],
			'startDate' => $params['startDate'],
			'endDate' => $params['endDate'],
			'totalBadges' => count($regInfos),
			'batchCount' => $fullBatchCount + ($partialBatch? 1 : 0)
		);
	}
	
	private function getCellText($registrationId, $cell) {
		$text = '';
		
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $registrationId);
		
		// don't print text after an empty field. for example, <City>, <State>, <Country> should 
		// look like: "Paris, France" if no <State> is given.
		$prevFieldInfo = array('index' => '', 'value' => '');
		
		foreach($cell['content'] as $index => $subCell) {
			if($subCell['showRegType'] === 'T') {
				$regType = db_RegTypeManager::getInstance()->find(array(
					'eventId' => $registration['eventId'],
					'id' => $registration['regTypeId']
				));
				$text .= $regType['description'];
			}
			else if($subCell['showLeadNumber'] === 'T') {
				$text .= model_Registrant::getLeadNumber($registration);
			}
			else if(empty($subCell['contactFieldId'])) {
				if($prevFieldInfo['index'] !== ($index-1) || !StringUtil::isBlank($prevFieldInfo['value'])) {
					$text .= $subCell['text'];
				}
			}
			else {
				// get registrant value for contact field.
				$field = db_ContactFieldManager::getInstance()->find(array(
					'eventId' => $registration['eventId'],
					'id' =>	$subCell['contactFieldId']
				));
				
				$value = model_Registrant::getInformationValue($registration, $field);
			
				// option fields may have multiple values selected.
				if(model_FormInput::isOptionInput($field['formInput']['id'])) {
					$value = $this->getOptionFieldValue($field, $value);
				}
				
				$prevFieldInfo = array('index' => $index, 'value' => $value);
				
				$text .= $value;
			}
		}
		
		return $text;
	}
	
	private function getBarcodeText($registrationId, $cell) {
		$text = '';
		
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $registrationId);
		
		foreach($cell['barcodeFields'] as $barcodeField) {
			$field = db_ContactFieldManager::getInstance()->find(array(
				'eventId' => $registration['eventId'],
				'id' => $barcodeField['contactFieldId']
			));
			
			$value = model_Registrant::getInformationValue($registration, $field);
			
			// option fields may have multiple values selected.
			if(model_FormInput::isOptionInput($field['formInput']['id'])) {
				$value = $this->getOptionFieldValue($field, $value);
			}
			
			$text .= $value.'^';
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
	
	private function getOptionFieldValue($field, $value) {
		// the value for fields that can have multiple values 
		// (select and checkbox) will be an array.
		if(is_array($value)) {
			$optionNames = array();
			foreach($value as $optionId) {
				$option = db_ContactFieldOptionManager::getInstance()->find(array(
					'eventId' => $field['eventId'],
					'id' => $optionId
				));
				
				$optionNames[] = $option['displayName'];
			}
			$value = implode(', ', $optionNames);
		}
		else if(!StringUtil::isBlank($value)) {
			$option = db_ContactFieldOptionManager::getInstance()->find(array(
				'eventId' => $field['eventId'],
				'id' => $value
			));
			
			$value = $option['displayName'];
		}
		
		return '';
	}
}

?>