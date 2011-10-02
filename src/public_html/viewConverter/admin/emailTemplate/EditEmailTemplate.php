<?php

class viewConverter_admin_emailTemplate_EditEmailTemplate extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Email Template';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= $this->getFileContents('page_admin_emailTemplate_EditEmailTemplate');
		
		return $body;
	}
	
	public function getSaveEmailTemplate($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getSendTestEmail($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>