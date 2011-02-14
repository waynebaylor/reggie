<?php

class action_admin_report_PaymentsToDate extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_PaymentsToDate();
		$this->converter = new viewConverter_admin_report_PaymentsToDate();	
	}
	
	public function view() {
		$eventId = RequestUtil::getValue('eventId', 0);

		$reportInfo = $this->logic->view($eventId);
		
		return $this->converter->getView(array('data' => $reportInfo));
	}
	
	public function csv() {
		$eventId = RequestUtil::getValue('eventId', 0);

		$reportInfo = $this->logic->csv($eventId);
		
		return $this->converter->getCsv(array('data' => $reportInfo));
	}
}

?>