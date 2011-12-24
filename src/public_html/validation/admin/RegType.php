<?php

class validation_admin_RegType
{
	public static function getConfig() {
		return array(
			validation_Validator::required('description', 'Description is required.'),
			validation_Validator::required('code', 'Code is required.'),
			validation_Validator::pattern('code', '/^[A-Za-z0-9]+$/', 'Code can only contain letters and numbers.')
		);
	}
}

?>