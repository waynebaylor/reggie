<?php

class action_admin_report_Reports extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_Reports();
		$this->converter = new viewConverter_admin_report_Reports();
	}
	
	public function view() {
		$eventId = RequestUtil::getValue('id', 0);
		
		$info = $this->logic->view($eventId);
		
		return $this->converter->getView($info);
	}
	
	public function addReport() {
		$errors = validation_Validator::validate(validation_admin_Report::getConfig(), array(
			'name' => RequestUtil::getValue('name', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$eventId = RequestUtil::getValue('eventId', 0);
		$reportName = RequestUtil::getValue('name', '');
		
		$info = $this->logic->addReport($eventId, $reportName);
		
		return $this->converter->getAddReport($info);
	}
	
	public function removeReport() {
		$reportId = RequestUtil::getValue('id', 0);
		
		$info = $this->logic->removeReport($reportId);
		
		return $this->converter->getRemoveReport($info);
	}
}

?>