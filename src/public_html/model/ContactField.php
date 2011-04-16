<?php

class model_ContactField
{
	public static function isVisibleTo($field, $regType) {	
		if($field['visibleToAll']) {
			return true;
		}

		foreach($field['visibleTo'] as $fieldType) { 
			if(intval($fieldType['id'], 10) === intval($regType['id'], 10)) {
				return true;
			}
		}
		
		return false;
	}
	
	public static function isRequired($field) {
		foreach($field['validationRules'] as $rule) {
			if(!empty($rule['value'])) {
				if(intval($rule['id'], 10) === model_Validation::$REQUIRED) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * if field is text or textarea, then an empty string is returned; if field has options, then an array containing
	 * the default selected option ids is returned.
	 * @param array $field
	 */
	public static function getDefaultValue($field) {
		if(in_array($field['formInput']['id'], array(model_FormInput::$CHECKBOX, model_FormInput::$RADIO, model_FormInput::$SELECT))) {
			$optIds = array();
			foreach($field['options'] as $opt) {
				if($opt['defaultSelected'] === 'T') {
					$optIds[] = $opt['id'];
				}
			}
			
			return $optIds;
		}
		else {
			return '';
		}
	}
}

?>