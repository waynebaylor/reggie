<?php

class fragment_Success extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	public function html() {
		return $this->HTML->hidden(array(
			'id' => 'xhr-response',
			'name' => 'success',
			'value' => 'true'
		));
	}
}

?>