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
}

?>