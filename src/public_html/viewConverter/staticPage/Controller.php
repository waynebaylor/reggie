<?php

class viewConverter_staticPage_Controller extends viewConverter_ViewConverter
{
	function __construct() {
		parent::__construct();
	}
	
	protected function head() {
		return <<<_
			<title>{$this->title}</title>	
_;
	}
	
	protected function body() {
		return <<<_
			<div id="content">
				{$this->content}
			</div>	
_;
	}
}

?>