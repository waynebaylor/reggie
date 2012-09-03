<?php

class action_admin_Feedback extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_Feedback();
		$this->converter = new viewConverter_admin_Feedback();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN,
			model_Role::$EVENT_MANAGER,
			model_Role::$EVENT_REGISTRAR,
			model_Role::$USER_ADMIN,
			model_Role::$VIEW_EVENT
		));
		
		return $hasRole;
	}
	
	public function view() {
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$info = $this->logic->view(array());
		return $this->converter->getView($info);
	}
	
	public function submitFeedback() {
		$params = RequestUtil::getParameters(array(
			'feedback'
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user);
		
		$info = $this->logic->submitFeedback($params);
		return $this->converter->getSubmitFeedback($info);
	}
}

?>