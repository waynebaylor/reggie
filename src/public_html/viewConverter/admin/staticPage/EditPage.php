<?php

class viewConverter_admin_staticPage_EditPage extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title = 'Edit Page Content';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_staticPage_EditPage');
		
		return $body;
	}
	
	protected function getBreadcrumbs() {
		return '';
	}
	
	public function getSavePage($properties) {
		$this->setProperties($properties);
		
		return new fragment_Success();
	}
}

?>