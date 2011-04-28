<?php

class viewConverter_admin_staticPage_PageList extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title = 'Event Pages';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_staticPage_PageList');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		return '';
	}
	
	public function getAddPage($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_staticPage_List'));
	}
	
	public function getRemovePage($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_staticPage_List'));
	}
}

?>