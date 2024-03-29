<?php

class template_Redirect extends template_Template
{
	private $location;
	
	function __construct(/*string*/ $url) {
		parent::__construct();
		
		$this->location = $url;	
	}
	
	public function html() {
		header('Location: '.$this->contextUrl($this->location));
		return '';
	}
}

?>