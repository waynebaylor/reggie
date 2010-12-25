<?php

class action_admin_registration_Registration extends action_ValidatorAction
{
	public function view() {
		$groupId = RequestUtil::getValue('groupId', 0);
		
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), $groupId);
		
		$regs = $group['registrations'];
		$firstReg = current($regs);
		$event = $this->strictFindById(db_EventManager::getInstance(), $firstReg['eventId']);
		
		return new template_admin_EditRegistrations($event, $group);	
	}
	
	public function save() {
		
		return new fragment_Success();
	}
}

?>