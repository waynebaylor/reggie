<?php

class badgeTemplateType_ThreeByFourDouble extends badgeTemplateType_BaseTemplate
{
	function __construct() {
		parent::__construct();
		
		$this->badgeWidth = 8.0; 	// inches
		$this->badgeHeight = 3.0;	// inches
		
		$this->topMargin = 1.0 + 0.375;		// inches (3/8in fudge factor)
		$this->sideMargin = 0.25 - 0.125;	// inches (1/8in fudge factor)
		
		// 3-up.
		$this->badgeCoords = array(
			array(0, 0),
			array(0, 1),
			array(0, 2)
		);
	}
}

?>