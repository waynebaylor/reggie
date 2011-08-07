<?php

class viewConverter_admin_dashboard_Users extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Manage Users';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_dashboard_users_Main');
		
		return $body;
	}
	
	public function getListUsers($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_dashboard_users_List'));
	}
	
	public function getDeleteUsers($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper('{success: "true"}');
	}
}

?>