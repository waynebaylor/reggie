<?php

class action_admin_badge_PrintBadge extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function singleBadge() {
		error_log(print_r($_REQUEST,true));
		return new fragment_Empty();
	}
}

?>