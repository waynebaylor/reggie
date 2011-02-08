<?php

class viewConverter_admin_emailTemplate_EditEmailTemplate extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Email Template';
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
	
	protected function getBreadcrumbs() {
		$info = db_BreadcrumbManager::getInstance()->findEditEmailTemplateCrumbs($this->emailTemplate['id']);
		
		$crumbs = new fragment_Breadcrumb(array(
			'location' => 'EditEmailTemplate',
			'eventId' => $this->emailTemplate['eventId'],
			'eventCode' => $info['code']
		));
		
		return $crumbs->html();
	}
}

?>