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
		
		$data = array();
		foreach($badgeTemplate['cells'] as $cell) {
			if($cell['hasBarcode'] === 'T') {
				$data[] = array(
					'isBarcode' => true,
					'xCoord' => $cell['xCoord'],
					'yCoord' => $cell['yCoord'],
					'text' => $this->getBarcodeText($params['registrationId'], $cell),
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
					'text' => $this->getCellText($params['registrationId'], $cell),
					'align' => $cell['horizontalAlign']
				);
			}
		}
		
		return array(
			'badgeTemplate' => $badgeTemplate,
			'eventInfo' => $eventInfo,
			'user' => $user,
			'data' => $data
		);
	}
	
	private function getCellText($registrationId, $cell) {
		$text = '';
		
		$registration = $this->strictFindById(db_reg_RegistrationManager::getInstance(), $registrationId);
		
		foreach($cell['content'] as $subCell) {
			if(empty($subCell['contactFieldId'])) {
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
			
			$text .= '['.$field['displayName'].':'.$value.']';
		}
 		
		return $text;
	}
}

?>