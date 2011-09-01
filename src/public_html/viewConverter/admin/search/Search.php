<?php

class viewConverter_admin_search_Search extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = "Search Results";
	}
	
	protected function body() {
		$body = parent::body();
		
		return $body;
	}
}

?>