<?php

class viewConverter_admin_dashboard_MainMenu extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Main Menu';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_dashboard_MainMenu');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		return '';
	}
	
	public function getAddEvent($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_dashboard_EventList'));
	}
	
	public function getAddUser($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_dashboard_UserList'));
	}
	
	public function getRemoveUser($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_dashboard_UserList'));
	}
}

?>