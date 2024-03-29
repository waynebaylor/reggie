<?php

class viewConverter_admin_report_EditReport extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Report';
	}
	
	protected function body() {
		$body = parent::body();
	
		$body .= $this->getFileContents('page_admin_report_EditReport');
		
		return $body;
	}
	
	public function getSaveReport($properties) {
		$this->setProperties($properties);
		
		return new fragment_Success();
	}
	
	public function getAddField($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_report_FieldList'));
	}
	
	public function getRemoveField($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_report_FieldList'));
	}
	
	public function getMoveFieldUp($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_report_FieldList'));
	}
	
	public function getMoveFieldDown($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_report_FieldList'));
	}
}

?>