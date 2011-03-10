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
	
	public function getAddEvent($info) {
		$this->setProperties($info);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_dashboard_EventList'));
	}
	
	public function getAddUser($info) {
		$this->setProperties($info);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_dashboard_UserList'));
	}
	
	public function getRemoveUser($info) {
		$this->setProperties($info);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_dashboard_UserList'));
	}
}

?>