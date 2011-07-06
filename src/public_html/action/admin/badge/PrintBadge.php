<?php

class action_admin_badge_PrintBadge extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_badge_PrintBadge();
		$this->converter = new viewConverter_admin_badge_PrintBadge();
	}
	
	public function singleBadge() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0, 
			'registrationId' => 0,
			'badgeTemplateId' => 0,
			'margins' => 'T',
			'shiftRight' => 0,
			'shiftDown' => 0
		));
		
		$info = $this->logic->singleBadge($params);
		return $this->converter->getSingleBadge($info);		
	}
	
	public function allBadges() {
		
	}
}

?>