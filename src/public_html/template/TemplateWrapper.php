<?php

class template_TemplateWrapper extends template_Template
{
	function __construct($html) {
		parent::__construct();
		
		$this->html = $html;
	}
	
	public function html() {
		return $this->html;
	}
}