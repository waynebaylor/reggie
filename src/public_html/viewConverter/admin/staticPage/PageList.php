<?php

class viewConverter_admin_staticPage_PageList extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_staticPage_PageList');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		return '';
	}
}

?>