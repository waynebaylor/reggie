<?php

class validation_Validator
{
	public static function validate($config, $values) {
		$messages = array();
		
		foreach($values as $fieldName => $value) {
			foreach($config as $restriction) {
				// limit to only one error per field.
				if($restriction->field === $fieldName && !in_array($fieldName, $messages) && !self::isValid($restriction, $value)) {
					// append [] for array values. this will match the input name in the html form.
					if(is_array($value)) {
						$fieldName = $fieldName.'[]'; 
					}
					$messages[$fieldName] = $restriction->text;
					break;
				}
			}
		}
		
		return $messages;
	}
	
	private static function isValid($restriction, $value) {
		switch($restriction->validationType) {
			case 'required': 
				return isset($value) && $value !== '';
			case 'pattern':
				return preg_match($restriction->regex, $value);
			default:
				throw new Exception('Unknown validation type: '.$restriction->validationType);
		}
	}
	
	public static function pattern($name, $regex, $text) {
		if(empty($name)) {
			throw new Exception('Field name required for "pattern" validation.');
		}
		
		$r = new stdClass();
		$r->validationType = 'pattern';
		$r->field = $name;
		$r->regex = $regex;
		$r->text = $text;
		
		return $r;
	}
	
	public static function required($name, $text) {
		if(empty($name)) {
			throw new Exception('Field name required for "required" validation.');
		}
		
		if(empty($text)) {
			$text = $name.' is required.';
		}
		
		$r = new stdClass();
		$r->validationType = 'required';
		$r->field = $name;
		$r->text = $text;
		
		return $r;
	}
}

?>