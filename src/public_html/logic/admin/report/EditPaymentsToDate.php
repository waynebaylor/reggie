<?php

class logic_admin_report_EditPaymentsToDate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($eventId) {
		return db_ReportManager::getInstance()->findPaymentsToDate($eventId);
	}
	
	public function saveReport() {
		
	}
	
	public function addField() {
		
	}
	
	public function removeField() {
		
	}
}

?>