<?php

class ValidationUtil
{
	public static function validate($fields) {
		$messages = array();
		
		foreach($fields as $field) {
			$name = $field['name'];
			$value = $field['value'];
			$restrictions = $field['restrictions'];
			foreach($restrictions as $r) {
				if(!self::isValid($r, $value)) {
					$messages[$name] = $r['text'];
					break;
				}
			}	
		}
		
		return $messages;
	}
	
	private static function isValid($restriction, $value) {
		switch($restriction['name']) {
			case 'required':
				return !empty($value);
			case 'pattern':
				return preg_match($restriction['regex'], $value);
			default:
				throw new Exception('Unknown validation type: '.$restriction['name']);
		}
	}
}

?>