<?php

class logic_admin_event_Manage extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['id']);
		
		return array(
			'user' => $params['user'],
			'event' => $event
		);
	}
}

?>