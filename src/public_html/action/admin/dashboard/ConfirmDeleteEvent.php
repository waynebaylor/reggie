<?php

class action_admin_dashboard_ConfirmDeleteEvent extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_dashboard_ConfirmDeleteEvent();
		$this->converter = new viewConverter_admin_dashboard_ConfirmDeleteEvent();
	}
	
	public function view() {
		$info = $this->logic->view(SessionUtil::getUser(), RequestUtil::getValue('id', 0));
		
		return $this->converter->getView($info);
	}
	
	public function deleteEvent() {
		$info = $this->logic->deleteEvent(SessionUtil::getUser(), RequestUtil::getValue('id', 0));
		
		return $this->converter->getDeleteEvent($info);
	}
}

?>