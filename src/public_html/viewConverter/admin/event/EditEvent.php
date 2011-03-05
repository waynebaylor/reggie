<?php

class viewConverter_admin_event_EditEvent extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Event';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= $this->getFileContents('page_admin_event_EditEvent');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		$bc = new fragment_Breadcrumb(array(
			'location' => 'Event',
			'event' => $this->event
		));
		
		return $bc->html();
	}
	
	public function getSaveEvent($properties = array()) {
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