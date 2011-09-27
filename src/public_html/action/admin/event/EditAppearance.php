<?php

class action_admin_event_EditAppearance extends action_BaseAction
{
	function __construct() {
		parent::__construct(); 
		
		$this->logic = new logic_admin_event_EditAppearance();
		$this->converter = new viewConverter_admin_event_EditAppearance();
	}
	
	public static function checkRole($user, $eventId=0, $method='') {
		return action_admin_event_EditEvent::checkRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));

		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function saveAppearance() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'headerContent' => '',
			'footerContent' => '',
			'headerBackgroundColor' => 'ffffff',
			'footerBackgroundColor' => 'ffffff',
			'menuTitle' => '',
			'menuBackgroundColor' => 'ffffff',
			'backgroundColor' => 'ffffff',
			'formBackgroundColor' => 'ffffff',
			'buttonTextColor' => 'ffffff',
			'buttonBackgroundColor' => 'ffffff',
			'pageBackgroundColor' => 'ffffff',
			'menuTitleBackgroundColor' => 'ffffff',
			'menuHighlightColor' => 'ffffff'
		));

		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->saveAppearance($params);
		return $this->converter->getSaveAppearance($info);
	}
}

?>