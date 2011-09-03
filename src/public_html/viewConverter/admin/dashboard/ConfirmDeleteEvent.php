<?php

class viewConverter_admin_dashboard_ConfirmDeleteEvent extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Delete Event Confirmation';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_dashboard_ConfirmDeleteEvent');
		
		return $body;
	}
	
	public function getDeleteEvents($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect('/admin/Login');
	}
}

?>