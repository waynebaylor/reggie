<?php

class viewConverter_admin_event_EditEvent extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Event Registration Form';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= $this->getFileContents('page_admin_event_EditEvent');
		
		return $body;
	}
	
	public function getSaveEvent($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getAddPage($properties) {
		$this->setProperties($properties);
		$html = $this->getFileContents('page_admin_event_PageList');
		return new template_TemplateWrapper($html);
	}
	
	public function getRemovePage($properties) {
		$this->setProperties($properties);
		$html = $this->getFileContents('page_admin_event_PageList');
		return new template_TemplateWrapper($html);
	}
	
	public function getMovePageUp($properties) {
		$this->setProperties($properties);
		$html = $this->getFileContents('page_admin_event_PageList');
		return new template_TemplateWrapper($html);
	}
	
	public function getMovePageDown($properties) {
		$this->setProperties($properties);
		$html = $this->getFileContents('page_admin_event_PageList');
		return new template_TemplateWrapper($html);
	}
}

?>