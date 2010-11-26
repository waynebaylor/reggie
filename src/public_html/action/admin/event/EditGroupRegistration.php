<?php

class action_admin_event_EditGroupRegistration extends action_BaseAction
{
	function __construct() {
		parent::_construct();
	}
	
	public function view() {
		$id = RequestUtil::getValue('id', 0);
		$event = $this->strictFindById(db_EventManager::getInstance(), $id);
		
		return new template_admin_EditGroupRegistration($event);		
	}
	
	public function saveGroupReg() {
		$groupReg = RequestUtil::getParameters(array());
		
		db_GroupRegistrationManager::getInstance()->save($groupReg);
		
		return new fragment_Success();
	}
}

?>