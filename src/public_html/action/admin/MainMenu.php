<?php

class action_admin_MainMenu extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}

	public function view() {
		return new template_admin_MainMenu();
	}
}

?>