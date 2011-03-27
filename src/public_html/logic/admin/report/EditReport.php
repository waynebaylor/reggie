<?php

class logic_admin_report_EditReport extends logic_Performer
{
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
	
	public function view($reportId) {
		$report = $this->strictFindById(db_ReportManager::getInstance(), $reportId);
		$event = db_EventManager::getInstance()->find($report['eventId']);
		
		return array(
			'title' => 'Edit Report',
			'event' => $event,
			'report' => $report
		);
	}
	
	public function saveReport($reportId, $reportName) {
		$report = $this->strictFindById(db_ReportManager::getInstance(), $reportId);
		
		$report['name'] = $reportName;
		
		db_ReportManager::getInstance()->saveReport($report);
		
		return array();
	}
	
	public function addField($field) {
		if(in_array($field['contactFieldId'], $this->specialFields)) {
			$field['name'] = $field['contactFieldId'];
			db_ReportManager::getInstance()->addSpecialField($field);	
		}
		else {
			db_ReportFieldManager::getInstance()->createField($field);
		}

		return array(
			'report' => db_ReportManager::getInstance()->find($field['reportId'])
		);
	}
	
	public function removeField($reportId, $id) {
		if(in_array($id, $this->specialFields)) {
			$field = array(
				'name' => $id,
				'reportId' => $reportId
			);
			
			db_ReportManager::getInstance()->removeSpecialField($field);
		}
		else {
			$field = $this->strictFindById(db_ReportFieldManager::getInstance(), $id);
			db_ReportFieldManager::getInstance()->deleteField($field);
		}
		
		return array(
			'report' => db_ReportManager::getInstance()->find($field['reportId'])
		);
	}
	
	public function moveFieldUp($fieldId) {
		$field = $this->strictFindById(db_ReportFieldManager::getInstance(), $fieldId);
		
		db_ReportFieldManager::getInstance()->moveFieldUp($field);
		
		return array(
			'report' => db_ReportManager::getInstance()->find($field['reportId'])
		);
	}
	
	public function moveFieldDown($fieldId) {
		$field = $this->strictFindById(db_ReportFieldManager::getInstance(), $fieldId);
		
		db_ReportFieldManager::getInstance()->moveFieldDown($field);
		
		return array(
			'report' => db_ReportManager::getInstance()->find($field['reportId'])
		);
	}
}

?>