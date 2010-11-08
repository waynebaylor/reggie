<?php

class action_admin_event_EditAppearance extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$id = $_REQUEST['eventId'];
		$event = db_EventManager::getInstance()->find($id);
		
		if(empty($event)) {
			return new template_ErrorPage();
		}
		
		return new template_admin_EditAppearance($event);
	}
	
	public function saveAppearance() {
		db_AppearanceManager::getInstance()->save($_REQUEST);	

		return new fragment_Success();
	}
}

?>