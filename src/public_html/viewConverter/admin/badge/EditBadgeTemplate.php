<?php

class viewConverter_admin_badge_EditBadgeTemplate extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title = 'Edit Badge Template';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_badge_EditBadgeTemplate');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		return '';
	}
}

?>