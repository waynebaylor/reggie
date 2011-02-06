<?php

class viewConverter_admin_emailTemplate_EmailTemplates extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Email Templates';
		$this->breadcrumbs = $this->getBreadcrumbs();
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= $this->getFileContents('page_admin_emailTemplate_EmailTemplates');
		
		return $body;
	}
	
	private function getBreadcrumbs() {
		return ''; //FIXME
	}
	
	public function getAddEmailTemplate($properties) {
		$this->setProperties($properties);
		
		$list = $this->getFileContents('page_admin_emailTemplate_List');
		
		return new template_TemplateWrapper($list);
	}
}

?>