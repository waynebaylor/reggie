<?php

class action_admin_report_Report extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function eventReports() {
		$event = $this->strictFindById(db_EventManager::getInstance(), RequestUtil::getValue('id', 0));

		return new template_admin_Reports($event);
	}
	
	public function view() {
		$event = $this->strictFindById(db_EventManager::getInstance(), RequestUtil::getValue('eventId', 0));
		$report = $this->strictFindById(db_ReportManager::getInstance(), RequestUtil::getValue('id', 0));

		return new template_admin_EditReport($event, $report);
	}
	
	public function addReport() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$event = $this->strictFindById(db_EventManager::getInstance(), RequestUtil::getValue('eventId', 0));
		
		$report = RequestUtil::getParameters(array('eventId', 'name'));
		db_ReportManager::getInstance()->createReport($report);
		
		$event = $this->strictFindById(db_EventManager::getInstance(), RequestUtil::getValue('eventId', 0));
		 
		return new fragment_report_List($event);
	}
	
	public function removeReport() {
		$report = $this->strictFindById(db_ReportManager::getInstance(), RequestUtil::getValue('id', 0));
		
		db_ReportManager::getInstance()->deleteReport($report);
		
		$event = $this->strictFindById(db_EventManager::getInstance(), $report['eventId']);
		 
		return new fragment_report_List($event);
	}
	
	public function saveReport() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$report = $this->strictFindById(db_ReportManager::getInstance(), RequestUtil::getValue('id', 0));
		
		$report['name'] = RequestUtil::getValue('name', '');
		
		db_ReportManager::getInstance()->saveReport($report);
		
		return new fragment_Success();
	}
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'name',
				'value' => RequestUtil::getValue('name', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Report Name is required.'
					)
				)
			)
		);
	}
}

?>