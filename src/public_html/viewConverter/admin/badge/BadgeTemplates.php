<?php

class viewConverter_admin_badge_BadgeTemplates extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title = 'Badge Templates';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_badge_BadgeTemplates');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		$b = new fragment_Breadcrumb(array(
			'location' => 'BadgeTemplates',
			'eventCode' => $this->eventCode
		));
		
		return $b->html();		
	}
	
	public function getAddTemplate($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_badge_TemplateList'));
	}
	
	public function getRemoveTemplate($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_badge_TemplateList'));
	}
	
	public function getCopyTemplate($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect('/admin/badge/BadgeTemplates?a=view&eventId='.$this->eventId);
	}
}

?>