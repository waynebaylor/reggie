<?php

class action_admin_report_ReportField extends action_BaseAction
{
	private $specialFields;
	
	function __construct() {
		parent::__construct();
		
		$this->specialFields = array(
			'date_registered',
			'date_cancelled',
			'category',
			'registration_type',
			'total_cost',
			'total_paid',
			'remaining_balance'
		);
	}
	
	public function addField() {
		$field = RequestUtil::getParameters(array('reportId', 'contactFieldId'));
		
		if(in_array($field['contactFieldId'], $this->specialFields)) {
			$field['name'] = $field['contactFieldId'];
			db_ReportManager::getInstance()->addSpecialField($field);	
		}
		else {
			db_ReportFieldManager::getInstance()->createField($field);
		}

		$report = db_ReportManager::getInstance()->find($field['reportId']);
		
		return new fragment_report_field_List($report);
	}
	
	public function removeField() {
		$id = RequestUtil::getValue('id', 0);
		
		if(in_array($id, $this->specialFields)) {
			$field = array(
				'name' => $id,
				'reportId' => RequestUtil::getValue('reportId', 0)
			);
			
			db_ReportManager::getInstance()->removeSpecialField($field);
		}
		else {
			$field = $this->strictFindById(db_ReportFieldManager::getInstance(), $id);
			db_ReportFieldManager::getInstance()->deleteField($field);
		}
		
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