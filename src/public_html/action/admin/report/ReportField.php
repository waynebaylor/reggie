<?php

class action_admin_report_ReportField extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function addField() {
		$field = RequestUtil::getParameters(array('reportId', 'contactFieldId'));
		
		db_ReportFieldManager::getInstance()->createField($field);
		
		$report = db_ReportManager::getInstance()->find($field['reportId']);
		
		return new fragment_report_field_List($report);
	}
	
	public function removeField() {
		$field = $this->strictFindById(db_ReportFieldManager::getInstance(), RequestUtil::getValue('id', 0));
		
		db_ReportFieldManager::getInstance()->deleteField($field);
		
		$report = db_ReportManager::getInstance()->find($field['reportId']);
		
		return new fragment_report_field_List($report);
	}
	
	public function moveFieldUp() {
		$field = $this->strictFindById(db_ReportFieldManager::getInstance(), RequestUtil::getValue('id', 0));
		
		db_ReportFieldManager::getInstance()->moveFieldUp($field);
		
		$report = db_ReportManager::getInstance()->find($field['reportId']);
		
		return new fragment_report_field_List($report);
	}
	
	public function moveFieldDown() {
		$field = $this->strictFindById(db_ReportFieldManager::getInstance(), RequestUtil::getValue('id', 0));
		
		db_ReportFieldManager::getInstance()->moveFieldDown($field);
		
		$report = db_ReportManager::getInstance()->find($field['reportId']);
		
		return new fragment_report_field_List($report);
	}
}

?>