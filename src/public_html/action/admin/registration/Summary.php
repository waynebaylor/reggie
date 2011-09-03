<?php

class action_admin_registration_Summary extends action_ValidatorAction
{
	public function view() {
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), RequestUtil::getValue('regGroupId', 0));
		$reportId = RequestUtil::getValue('reportId', 0);
		
		$r = reset($group['registrations']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $r['eventId']);
		
		if(empty($reportId)) {
			$report = reset($event['reports']); 
		}
		else {
			$report = db_ReportManager::getInstance()->find($reportId);
		}
		
		return new template_admin_GroupSummary($event, $report, $group);
	}
}

?>