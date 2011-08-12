<?php

class action_admin_event_Manage extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_Manage();
		$this->converter = new viewConverter_admin_event_Manage();
	}
	
	public function view() {
		
	}
}

?>