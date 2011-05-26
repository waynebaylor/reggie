<?php

class validation_admin_BadgeTemplate
{
	public static function getConfig() {
		return array(
			validation_Validator::required('name', 'Name is required.'),
			validation_Validator::required('regTypeIds', 'Registration Types are required.')
		);
	}
}

?>