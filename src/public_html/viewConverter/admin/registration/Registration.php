<?php

class viewConverter_admin_registration_Registration extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getDeleteRegistration($properties) {
		$this->setProperties($properties);
		
		if($this->isGroupEmpty) {
			return new template_Redirect("/admin/report/GenerateReport?id={$this->reportId}");	
		}
		else {
			return new template_Redirect("/admin/registration/Registration?groupId={$this->regGroupId}&reportId={$this->reportId}");
		}
	}
}

?>