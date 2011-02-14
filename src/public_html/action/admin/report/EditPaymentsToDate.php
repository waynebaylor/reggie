<?php

class action_admin_report_EditPaymentsToDate extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_EditPaymentsToDate();
		$this->converter = new viewConverter_admin_report_EditPaymentsToDate();
	}
	
	public function view() {
		$eventId = RequestUtil::getValue('eventId', 0);
		
		$report = $this->logic->view($eventId);
		return $this->converter->getView(array(
			'report' => $report
		));
	}
	
	public function addField() {
		
	}
	
	public function removeField() {
		
	}
	
	public function moveFieldUp() {
		
	}
	
	public function moveFieldDown() {
		
	}
}

?>