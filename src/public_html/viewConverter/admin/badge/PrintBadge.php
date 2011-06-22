<?php

class viewConverter_admin_badge_PrintBadge extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getSingleBadge($properties) {
		$this->setProperties($properties);
		
		$printTemplate = model_BadgeTemplateType::newTemplate($this->badgeTemplate['type']);
		$printTemplate->getPdfSingle($this->user, $this->eventInfo, $this->data);
		
		return new fragment_Empty();
	}
}

?>