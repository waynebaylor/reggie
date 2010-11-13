<?php

class fragment_reg_Closed extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		return $this->event['regClosedText'];
	}
}

?>