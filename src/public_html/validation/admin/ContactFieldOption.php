<?php

class validation_admin_ContactFieldOption
{
	public static function getConfig() {
		return array(
			validation_Validator::required('displayName', 'Label is required.')
		);
	}
}

?>