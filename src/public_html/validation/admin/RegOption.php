<?php

class validation_admin_RegOption
{
	public static function getOptionConfig() {
		return array(
			validation_Validator::required('code', 'Code is required.'),
			validation_Validator::pattern('code', '/^[A-Za-z0-9]+$/', 'Code can only contain letters and numbers.'),
			validation_Validator::required('description', 'Description is required.')
		);
	}
	
	public static function getTextConfig() {
		return array(
			validation_Validator::required('text', 'Text is required.')
		);
	}
}

?>