<?php

class viewConverter_admin_data_User extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getCurrentUser($properties) {
		$this->setProperties($properties);
		
		$html = $this->getFileContents('page_admin_data_User');
		
		return new template_TemplateWrapper($html);
	}
}

?>