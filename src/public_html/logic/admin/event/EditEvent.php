<?php

class logic_admin_event_EditEvent extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($id) {
		return $this->strictFindById(db_EventManager::getInstance(), $id);
	}
}
		
?>