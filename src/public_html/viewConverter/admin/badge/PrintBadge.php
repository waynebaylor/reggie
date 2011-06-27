<?php

class viewConverter_admin_badge_PrintBadge extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getSingleBadge($properties) {
		$this->setProperties($properties);
		
		$printTemplate = model_BadgeTemplateType::newTemplate($this->badgeTemplate['type']);
		$printTemplate->getPdfSingle(array(
			'user' => $this->user, 
			'event' => $this->eventInfo, 
			'data' => $this->data,
			'margins' => $this->margins,
			'shiftRight' => $this->shiftRight,
			'shiftDown' => $this->shiftDown
		));
		
		return new fragment_Empty();
	}
}

?>