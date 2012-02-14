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
		$report = db_ReportManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['reportId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $event['id'],
			'event' => $event,
			'report' => $report
		);
	}
	
	public function saveReport($params) {
		db_ReportManager::getInstance()->saveReport($params);
		
		return $params;
	}
	
	public function addField($params) {
		if(in_array($params['contactFieldId'], $this->specialFields)) {
			db_ReportManager::getInstance()->addSpecialField(array(
				'eventId' => $params['eventId'],
				'reportId' => $params['reportId'],
				'fieldName' => $params['contactFieldId']
			));	
		}
		else {
			db_ReportFieldManager::getInstance()->createField($params);
		}

		return array(
			'report' => db_ReportManager::getInstance()->find(array(
				'eventId' => $params['eventId'],
				'id' => $params['reportId']
			))		
		);
	}
	
	public function removeField($params) {
		if(in_array($params['id'], $this->specialFields)) {
			db_ReportManager::getInstance()->removeSpecialField(array(
				'eventId' => $params['eventId'],
				'reportId' => $params['reportId'],
				'fieldName' => $params['id']
			));
		}
		else {
			db_ReportFieldManager::getInstance()->deleteField($params);
		}
		
		return array(
			'eventId' => $params['eventId'],
			'report' => db_ReportManager::getInstance()->find(array(
				'eventId' => $params['eventId'], 
				'id' => $params['reportId']
			))
		);
	}
	
	public function moveFieldUp($params) {
		$field = db_ReportFieldManager::getInstance()->find($params);
		
		$field['eventId'] = $params['eventId'];
		
		db_ReportFieldManager::getInstance()->moveFieldUp($field);
		
		return array(
			'eventId' => $params['eventId'],
			'report' => db_ReportManager::getInstance()->find(array(
				'eventId' => $params['eventId'], 
				'id' => $field['reportId']
			))
		);
	}
	
	public function moveFieldDown($params) {
		$field = db_ReportFieldManager::getInstance()->find($params);
		
		$field['eventId'] = $params['eventId'];
		
		db_ReportFieldManager::getInstance()->moveFieldDown($field);
		
		return array(
			'eventId' => $params['eventId'],
			'report' => db_ReportManager::getInstance()->find(array(
				'eventId' => $params['eventId'], 
				'id' => $field['reportId']
			))
		);
	}
}

?>