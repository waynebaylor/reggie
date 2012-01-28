<?php

class action_admin_badge_PrintBadge extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_badge_PrintBadge();
		$this->converter = new viewConverter_admin_badge_PrintBadge();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_badge_BadgeTemplates();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function singleBadge() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0, 
			'registrationId' => 0,
			'badgeTemplateId' => 0,
			'shiftRight' => 0,
			'shiftDown' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->singleBadge($params);
		return $this->converter->getSingleBadge($info);		
	}
	
	public function allBadges() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'sortByFieldId' => 0,
			'templateIds' => array(),
			'batchNumber' => -1,
			'startDate' => '',
			'endDate' => ''
		));

		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->allBadges($params);
		return $this->converter->getAllBadges($info);
	}
	
	public function batchCount() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'sortByFieldId' => 0,
			'templateIds' => array(),
			'startDate' => '',
			'endDate' => ''
		));	
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->batchCount($params);
		return $this->converter->getBatchCount($info);
	}
}

?>