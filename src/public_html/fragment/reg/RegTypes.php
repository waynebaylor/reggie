<?php

class fragment_reg_RegTypes extends template_Template
{
	private $regTypes;
	
	function __construct($regTypes) {
		parent::__construct();

		$this->regTypes = $regTypes;
	}		
	
	public function html() {
		$html = '<div>';
	
		foreach($this->regTypes as $regType) {
			$r = new fragment_reg_RegType($regType);
			$html .= $r->html();	
		}
		
		return $html.'</div>';
	}
}

?>