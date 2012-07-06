<?php

class logic_admin_registration_Summary extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$group = db_reg_GroupManager::getInstance()->find($params['regGroupId']);
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $event['id'],
			'event' => $event,
			'group' => $group,
			'showDetailsLink' => $this->getShowDetailsLink($params['user'], $params['eventId'])
		);
	}
	
	public function printPdf($params) {
		$group = db_reg_GroupManager::getInstance()->find($params['id']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'event' => $event,
			'group' => $group
		);
	}
	
	private function getShowDetailsLink($user, $eventId) {
		$a = new action_admin_registration_Registration();
		return $a->hasRole($user, $eventId);
	}
}

?>