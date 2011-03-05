<?php

class validation_admin_Page
{
	public static function getConfig() {
		return array(
			validation_Validator::required('title', 'Page Title is required.')
		);
	}
}

?>