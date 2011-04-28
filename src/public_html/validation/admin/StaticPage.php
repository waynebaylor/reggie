<?php

class validation_admin_StaticPage
{
	public static function getConfig() {
		return array(
			validation_Validator::required('name', 'Name is required.'),
			validation_Validator::pattern('name', '/^[_A-Za-z0-9]+$/', 'Name can only contain letters, numbers, and underscore.')
		);
	}
}

?>