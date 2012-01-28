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
	
	public function view($params) {
		$report = $this->strictFindById(db_ReportManager::getInstance(), $params['reportId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $event['id'],
			'event' => $event,
			'report' => $report
		);
	}
	
	public function saveReport($params) {
		$report = $this->strictFindById(db_ReportManager::getInstance(), $params['id']);
		
		$report['name'] = $params['name'];
		
		db_ReportManager::getInstance()->saveReport($report);
		
		return $params;
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
	
	public function removeField($params) {
		if(in_array($params['id'], $this->specialFields)) {
			$field = array(
				'name' => $params['id'],
				'reportId' => $params['reportId']
			);
			
			db_ReportManager::getInstance()->removeSpecialField($field);
		}
		else {
			$field = $this->strictFindById(db_ReportFieldManager::getInstance(), $params['id']);
			db_ReportFieldManager::getInstance()->deleteField($field);
		}
		
		return array(
			'eventId' => $params['eventId'],
			'report' => db_ReportManager::getInstance()->find($field['reportId'])
		);
	}
	
	public function moveFieldUp($params) {
		$field = $this->strictFindById(db_ReportFieldManager::getInstance(), $params['id']);
		
		db_ReportFieldManager::getInstance()->moveFieldUp($field);
		
		return array(
			'eventId' => $params['eventId'],
			'report' => db_ReportManager::getInstance()->find($field['reportId'])
		);
	}
	
	public function moveFieldDown($params) {
		$field = $this->strictFindById(db_ReportFieldManager::getInstance(), $params['id']);
		
		db_ReportFieldManager::getInstance()->moveFieldDown($field);
		
		return array(
			'eventId' => $params['eventId'],
			'report' => db_ReportManager::getInstance()->find($field['reportId'])
		);
	}
}

?>