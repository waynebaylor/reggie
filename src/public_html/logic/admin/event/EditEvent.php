<?php

class logic_admin_event_EditEvent extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($id) {
		return $this->strictFindById(db_EventManager::getInstance(), $id);
	}
	
	public function saveEvent($info) {
		$oldEvent = $this->strictFindById(db_EventManager::getInstance(), $info['id']);
	
		db_EventManager::getInstance()->save($info);
		
		FileUtil::renameEventDir($oldEvent, $info);
	}
}
		
?>