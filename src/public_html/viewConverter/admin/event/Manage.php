<?php

class viewConverter_admin_event_Manage extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	
		$this->title = 'Manage Event';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= $this->getFileContents('page_admin_event_Manage');
		
		return $body;
	}
}

?>