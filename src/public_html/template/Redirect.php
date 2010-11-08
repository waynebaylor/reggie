<?php

require_once 'template/Template.php';

class template_Redirect extends template_Template
{
	private $location;
	
	function __construct(/*string*/ $url) {
		$this->location = $url;	
	}
	
	public function html() {
		header('Location: '.$this->location);
		return '';
	}
}

?>