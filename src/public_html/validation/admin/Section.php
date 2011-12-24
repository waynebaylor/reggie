<?php

class validation_admin_Section
{
	public static function getConfig() {
		return array(
			validation_Validator::required('name', 'Name is required.'),
			validation_Validator::required('contentTypeId', 'Content is required.')
		);
	}
}

?>