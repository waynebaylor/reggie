<?php

class viewConverter_admin_emailTemplate_EditEmailTemplate extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Email Template';
		$this->breadcrumbs = $this->getBreadcrumbs();
	}
	
	public function getView($properties) {
		$template = $properties['emailTemplate'];

		$regTypeIds = array();
		
		if($template['availableToAll'] === true) {
			$regTypeIds[] = -1;	
		}
		else {
			foreach($template['availableTo'] as $regType) {
				$regTypeIds[] = $regType['id'];
			}
		}
		
		$properties['emailTemplate']['regTypeIds'] = $regTypeIds;
 		
		return parent::getView($properties);
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= $this->getFileContents('page_admin_emailTemplate_EditEmailTemplate');
		
		return $body;
	}
	
	private function getBreadcrumbs() {
		
	}
}

?>