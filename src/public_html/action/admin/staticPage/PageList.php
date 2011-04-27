<?php

class action_admin_staticPage_PageList extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_staticPage_PageList();
		$this->converter = new viewConverter_admin_staticPage_PageList();
	}
	
	public function view() {
		$eventId = RequestUtil::getValue('eventId', 0);
		
		$info = $this->logic->view(array(
			'eventId' => $eventId
		));
		
		return $this->converter->getView($info);
	}
	
	public function addPage() {
		
	}
	
	public function removePage() {
		
	}
}

?>