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
		
	}
}

?>