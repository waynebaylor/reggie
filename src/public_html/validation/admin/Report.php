<?php

class validation_admin_Report
{
	public static function getConfig() {
		return array(
			validation_Validator::required('name', 'Report Name is required.')
		);
	}
}

?>