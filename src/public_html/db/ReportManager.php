<?php

class db_ReportManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Report';
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);
		
		$obj['fields'] = db_ReportFieldManager::getInstance()->findByReport($obj);
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_ReportManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				eventId,
				name,
				showDateRegistered,
				showCategory,
				showRegType,
				showPaymentType,
				showTotalCost,
				showTotalPaid,
				showRemainingBalance
			FROM
				Report
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find report.');
	}
	
	public function findByEvent($event) {
		$sql = '
			SELECT
				id,
				eventId,
				name,
				showDateRegistered,
				showCategory,
				showRegType,
				showPaymentType,
				showTotalCost,
				showTotalPaid,
				showRemainingBalance
			FROM
				Report
			WHERE
				eventId=:eventId
		';
		
		$params = array(
			'eventId' => $event['id']
		);
		
		return $this->query($sql, $params, 'Find reports by event.');
	}
	
	public function createReport($report) {
		$sql = '
			INSERT INTO
				Report (
					eventId,
					name	
				)
			VALUES (
				:eventId,
				:name
			)
		';
		
		$params = array(
			'eventId' => $report['eventId'],
			'name' => $report['name']
		);
		
		$this->execute($sql, $params, 'Create report.');
		
		return $this->lastInsertId();
	}
	
	public function deleteReport($report) {
		$sql = '
			DELETE FROM
				Report
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $report['id']
		);
		
		$this->execute($sql, $params, 'Delete report.');
	}
	
	public function saveReport($report) {
		$sql = '
			UPDATE
				Report
			SET
				name=:name
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $report['id'],
			'name' => $report['name']
		);
		
		$this->execute($sql, $params, 'Save report.');
	}

	public function addSpecialField($field) {
		$specialFields = array(
			'date_registered' => 'showDateRegistered',
			'category' => 'showCategory',
			'registration_type' => 'showRegType',
			'payment_type' => 'showPaymentType',
			'total_cost' => 'showTotalCost',
			'total_paid' => 'showTotalPaid',
			'remaining_balance' => 'showRemainingBalance'
		);
		
		$sql = "
			UPDATE
				Report
			SET
				{$specialFields[$field['name']]} = :value
			WHERE
				id = :id
		";
				
		$params = array(
			'id' => $field['reportId'],
			'value' => 'true'
		);
		
		$this->execute($sql, $params, 'Add special field to report.');
	}
	
	public function removeSpecialField($field) {
		$specialFields = array(
			'date_registered' => 'showDateRegistered',
			'category' => 'showCategory',
			'registration_type' => 'showRegType',
			'payment_type' => 'showPaymentType',
			'total_cost' => 'showTotalCost',
			'total_paid' => 'showTotalPaid',
			'remaining_balance' => 'showRemainingBalance'
		);
		
		$sql = "
			UPDATE
				Report
			SET
				{$specialFields[$field['name']]} = :value
			WHERE
				id = :id
		";
				
		$params = array(
			'id' => $field['reportId'],
			'value' => 'false'
		);
		
		$this->execute($sql, $params, 'Remove special field from report.');
	}
	
	//////////////////////////////////////////////////////////////
	// methods for running reports.
	//////////////////////////////////////////////////////////////
	
	public function generateReport($report) {
		$sql = '
			SELECT
				Registration.id as registrationId,
				Registration.dateRegistered,
				Category.displayName as categoryName,
				RegType.description as regTypeName
			FROM
				Registration
			INNER JOIN
				Category
			ON
				Registration.categoryId = Category.id
			INNER JOIN
				RegType
			ON
				Registration.regTypeId = RegType.id
			INNER JOIN
				Report
			ON
				Report.eventId = Registration.eventId
			WHERE
				Report.id = :reportId
			ORDER BY
				Registration.dateRegistered
			DESC
		';
		
		$params = array(
			'reportId' => $report['id']
		);
		
		$results = $this->rawQuery($sql, $params, 'Find report results.');
		
		foreach($results as &$result) {
			$result['fieldValues'] = $this->getReportFieldValues($result['registrationId']);
		}

		return $results;
	}
	
	public function getReportFieldNames($report) {
		$sql = '
			SELECT 
				ContactField.id,
				ContactField.displayName
			FROM
				ContactField
			INNER JOIN
				Report_ContactField
			ON
				ContactField.id = Report_ContactField.contactFieldId
			WHERE
				Report_ContactField.reportId = :reportId
			ORDER BY
				Report_ContactField.displayOrder
		';
		
		$params = array(
			'reportId' => $report['id']
		);
		
		return $this->rawQuery($sql, $params, 'Find report contact field names.');
	}
	
	private function getReportFieldValues($registrationId) {
		// single input fields (text, textarea).
		$sql = '
			SELECT 
				ContactField.id,
				Registration_Information.value
			FROM
				ContactField
			INNER JOIN
				Registration_Information
			ON
				Registration_Information.contactFieldId = ContactField.id
			INNER JOIN
				Registration
			ON
				Registration_Information.registrationId = Registration.id
			INNER JOIN
				Report_ContactField
			ON
				Report_ContactField.contactFieldId = ContactField.id
			WHERE
				Registration.id = :registrationId
			AND
				ContactField.formInputId 
			IN
				(1, 2)
		';
		
		$params = array(
			'registrationId' => $registrationId
		);
		
		$results = $this->rawQuery($sql, $params, 'Find report field values.');
		
		$values = array();
		foreach($results as $result) {
			$values[$result['id']] = $result['value'];
		}
		
		// multiple input fields (checkbox, radio, select).
		$sql = '
			SELECT 
				ContactField.id,
				ContactFieldOption.displayName as value
			FROM
				ContactField
			INNER JOIN
				Registration_Information
			ON
				Registration_Information.contactFieldId = ContactField.id
			INNER JOIN
				Registration
			ON
				Registration_Information.registrationId = Registration.id
			INNER JOIN
				Report_ContactField
			ON
				Report_ContactField.contactFieldId = ContactField.id
			INNER JOIN
				ContactFieldOption
			ON
				Registration_Information.value = ContactFieldOption.id
			WHERE
				Registration.id = :registrationId
			AND
				ContactField.formInputId 
			IN
				(3, 4, 5)
		';
		
		$results = $this->rawQuery($sql, $params, 'Find report option field values.');
		
		foreach($results as $result) {
			if(empty($values[$result['id']])) {
				$values[$result['id']] = array();
			}
			
			$values[$result['id']][] = $result['value'];
		}
		
		return $values;
	}
}

?>