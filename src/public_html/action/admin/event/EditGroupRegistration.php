<?php

class action_admin_event_EditGroupRegistration extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$id = RequestUtil::getValue('id', 0);
		$event = $this->strictFindById(db_EventManager::getInstance(), $id);
		
		return new template_admin_EditGroupRegistration($event);		
	}
	
	public function saveGroupReg() {
		$groupReg = RequestUtil::getParameters(array(
			'id',
			'eventId'
		));
		
		$groupReg['enabled'] = RequestUtil::getValue('enabled', 'false');
		$groupReg['defaultRegType'] = RequestUtil::getValue('defaultRegType', 'false');
		
		db_GroupRegistrationManager::getInstance()->save($groupReg);
		
		return new fragment_Success();
	}
}

?>