<?php

class badgeTemplateType_ThreeByFour extends badgeTemplateType_BaseTemplate
{
	function __construct() {
		parent::__construct();
		
		$this->badgeWidth = 4.0;  // inches
		$this->badgeHeight = 3.0; // inches
		
		$this->topMargin = 1.0 + 0.375;		// inches (3/8in fudge factor)
		$this->sideMargin = 0.25 - 0.125;	// inches (1/8in fudge factor)
	}
	
	public function getPdfSingle($config) {
		
	}
}

?>