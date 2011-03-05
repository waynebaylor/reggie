<?php

class validation_admin_Event
{
	public static function getConfig() {
		return array(
			validation_Validator::required('code', 'Code is required.'),
			validation_Validator::pattern('code', '/^[A-Za-z]/', 'Code must begin with a letter.'),
			validation_Validator::pattern('code', '/^[A-Za-z][_A-Za-z0-9]*$/', 'Code can only contain letters, numbers, and underscore.'),
			
			validation_Validator::required('regOpen', 'Registration Open is required.'),
			validation_Validator::pattern('regOpen', '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2})?$/', 'Enter date as "yyyy-MM-dd" or "yyyy-MM-dd HH:mm".'),
			
			validation_Validator::required('regClosed', 'Registration Closed is required.'),
			validation_Validator::pattern('regClosed', '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2})?$/', 'Enter date as "yyyy-MM-dd" or "yyyy-MM-dd HH:mm".')
		);
	}
}

?>