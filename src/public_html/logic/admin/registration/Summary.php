<?php

class logic_admin_registration_Summary extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), $params['regGroupId']);
		$reportId = $params['reportId'];
		
		$r = reset($group['registrations']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $r['eventId']);
		
		if(empty($reportId)) {
			$report = reset($event['reports']); 
		}
		else {
			$report = db_ReportManager::getInstance()->find($reportId);
		}
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $event['id'],
			'event' => $event,
			'report' => $report,
			'group' => $group,
			'showDetailsLink' => $this->getShowDetailsLink($params['user'], $params['eventId'])
		);
	}
	
	private function getShowDetailsLink($user, $eventId) {
		$showDetailsLink = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN, 
			model_Role::$EVENT_ADMIN
		));
		
		$showDetailsLink = $showDetailsLink || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER,
			model_Role::$EVENT_REGISTRAR
		), $eventId);
		
		return $showDetailsLink;
	}
}

?>