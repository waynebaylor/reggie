<?php

class model_Registrant
{
	public static function getInformationValue($registrant, $field) {
		$information = $registrant['information'];
		foreach($information as $registrantField) {
			if($field['id'] === $registrantField['contactFieldId']) {
				return $registrantField['value'];
			}
		}
		
		return NULL;
	}
	
	public static function getRegOptionIds($registrant) {
		$optIds = array();
		
		foreach($registrant['regOptions'] as $opt) {
			$optIds[] = $opt['regOptionId'];
		}
		
		return $optIds;
	}
	
	public static function getVariableQuantityAmount($registrant, $varOptionId) {
		foreach($registrant['variableQuantity'] as $opt) {
			if($varOptionId == $opt['variableQuantityId']) {
				return $opt['quantity'];
			}
		}
		
		return 0;
	}
	
	public static function getVariableQuantityPriceId($registrant, $varOptionId) {
		foreach($registrant['variableQuantity'] as $opt) {
			if($varOptionId == $opt['variableQuantityId']) {
				return $opt['priceId'];
			}
		}
		
		return 0;
	}
	
	public static function getPriceId($registrant, $regOptionId) {
		foreach($registrant['regOptions'] as $opt) {
			if($regOptionId == $opt['regOptionId']) {
				return $opt['priceId'];
			}
		}
		
		return 0;
	}
	
	public static function isOptionCancelled($registrant, $regOptionId) {
		foreach($registrant['regOptions'] as $opt) {
			if($regOptionId == $opt['regOptionId']) {
				return !empty($opt['dateCancelled']);
			}
		}
		
		// if the registrant hasn't selected the given option.
		return true; 
	}
	
	public static function getEmailFieldValue($emailTemplate, $registrant) {
		$fieldId = $emailTemplate['contactFieldId'];
		return self::getInformationValue($registrant, array('id' => $fieldId));
	}
	
	public static function getConfirmationNumber($registrant) {
		$num = $registrant['confirmationNumber'];
		$length = max(array(8, strlen($num)%4 + strlen($num))); // need this many digits.
		$num = str_pad($num, $length, '0', STR_PAD_LEFT);
		
		return implode('-', str_split($num, 4)); // formatted like 0000-0000
	}
	
	public static function getLeadNumber($registrant) {
		// pad the front of the lead number with zeros.
		$num = '00000'.$registrant['leadNumber'];
		return substr($num, -5);
	}
}

?>