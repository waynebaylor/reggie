<?php

class fragment_Success extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	public function html() {
		return $this->HTML->textarea(array(
			'id' => 'xhr-response',
			'class' => 'hide',
			'name' => 'success',
			'value' => 'true'
		));
	}
}

?>