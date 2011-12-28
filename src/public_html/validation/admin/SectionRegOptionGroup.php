<?php

class validation_admin_SectionRegOptionGroup
{
	public static function getConfig() {
		return array();
	}
	
	public static function validate($values) {
		$errors = validation_Validator::validate(self::getConfig(), $values);
		
		if($values['multiple'] === 'T') {
			// don't let min/max start with 0, since octal numbers start with 0.
			
			$required = $values['required'];
			$minimum = $values['minimum'];
			
			// first check if minimum is valid.
			if(!preg_match('/^0|([1-9][0-9]*)$/', $minimum)) {
				$errors['minimum'] = 'Minimum must be 0 or more.';
			}
			// if required, then the minimum must be at least one.
			else if($required === 'T' && $minimum < 1) {
				$errors['minimum'] = 'Minimum must be 1 or more if Required.';
			}
			// if minimum is greater than 0, then required must be true.
			else if($required === 'F' && $minimum > 0) {
				$errors['minimum'] = 'Minimum must be 0 if not Required.';		
			}
			// maximum can't be less than minimum.
			else {
				$maximum = $values['maximum'];
				if(!preg_match('/^0|([1-9][0-9]*)$/', $maximum) || $maximum < $minimum) {
					$errors['maximum'] = 'Maximum must be greater or equal to Minimum.';
				}
			}
		}
		
		return $errors;
	}
}