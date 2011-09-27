<?php

class action_admin_staticPage_PageList extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_staticPage_PageList();
		$this->converter = new viewConverter_admin_staticPage_PageList();
	}
	
	public static function checkRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));	
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent($user, model_Role::$EVENT_MANAGER, $eventId);
		
		if(!$hasRole) {
			throw new Exception('User does not have required role.');
		}
	}
	
	public function view() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function listPages() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->listPages($params);
		return $this->converter->getListPages($info);
	}
	
	public function deletePages() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0),
			'pageIds' => RequestUtil::getValueAsArray('pageIds', array())
		);
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->deletePages($params);
		return $this->converter->getDeletePages($info);
	}
}

?>