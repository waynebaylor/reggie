<?php

class db_ReportManager extends db_Manager
{
	//
	// dbColumn = the Registration table column name.
	// dbValueColumn = the field name from the sql select. 
	// displayName = the column text on the report page.
	//
	// some of the payment fields don't have a dbValueColumn because
	// they must be computed outside of a sql query.
	//
	public static $SPECIAL_FIELDS = array(
		'date_registered' => array(
			'dbColumn' => 'showDateRegistered',
			'dbValueColumn' => 'dateRegistered',
			'displayName' => 'Date Registered'
		),
		'date_cancelled' => array(
			'dbColumn' => 'showDateCancelled',
			'dbValueColumn' => 'dateCancelled',
			'displayName' => 'Date Cancelled'
		),
		'category' => array(
			'dbColumn' => 'showCategory',
			'dbValueColumn' => 'categoryName',
			'displayName' => 'Category'
		),
		'registration_type' => array(
			'dbColumn' => 'showRegType',
			'dbValueColumn' => 'regTypeName',
			'displayName' => 'Registration Type'
		),
		'total_cost' => array(
			'dbColumn' => 'showTotalCost',
			'displayName' => 'Total Cost'
		),
		'total_paid' => array(
			'dbColumn' => 'showTotalPaid',
			'displayName' => 'Total Paid'
		),
		'remaining_balance' => array(
			'dbColumn' => 'showRemainingBalance',
			'displayName' => 'Remaining Balance'
		)
	);
	
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
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
				showDateCancelled,
				showCategory,
				showRegType,
				showTotalCost,
				showTotalPaid,
				showRemainingBalance,
				isPaymentsToDate
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
				showDateCancelled,
				showCategory,
				showRegType,
				showTotalCost,
				showTotalPaid,
				showRemainingBalance,
				isPaymentsToDate
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
		$column = self::$SPECIAL_FIELDS[$field['name']]['dbColumn'];
		
		$sql = "
			UPDATE
				Report
			SET
				{$column} = :value
			WHERE
				id = :id
		";
				
		$params = array(
			'id' => $field['reportId'],
			'value' => 'T'
		);
		
		$this->execute($sql, $params, 'Add special field to report.');
	}
	
	public function removeSpecialField($field) {
		$column = self::$SPECIAL_FIELDS[$field['name']]['dbColumn'];
		
		$sql = "
			UPDATE
				Report
			SET
				{$column} = :value
			WHERE
				id = :id
		";
				
		$params = array(
			'id' => $field['reportId'],
			'value' => 'F'
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
				Registration.regGroupId as groupId,
				Registration.dateRegistered,
				Registration.dateCancelled,
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
		
		//
		// put the fields together into something the report can use.
		//
		
		$fieldResults = array();
		
		$processedGroupIds = array(); // only display payment info for one registrant per group.
		foreach($results as $result) {
			$fieldValues = $this->getReportFieldValues($result);

			// can't use array_merge because $fieldValues have integer keys which will be
			// renumbered after the merge.
			$specialFieldValues = $this->getSpecialFieldValues($report, $result, $processedGroupIds);
			foreach($specialFieldValues as $key => $value) {
				$fieldValues[$key] = $value;
			}
			
			// this is used for the 'details' link.
			$fieldValues['details'] = $result['groupId'];
						
			$fieldResults[] = array('fieldValues' => $fieldValues);
		
			$processedGroupIds[] = $result['groupId'];
		}

		return $fieldResults;
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
		
		$results = $this->rawQuery($sql, $params, 'Find report contact field names.');

		//
		// prepend the general fields, if any. the order matters because they are prepended 
		// to the front of the results array.
		//
		foreach(array('registration_type', 'category', 'date_cancelled', 'date_registered') as $specialFieldId) {
			$column = self::$SPECIAL_FIELDS[$specialFieldId]['dbColumn'];
			if($report[$column] === 'T') {
				$field = array(
					'id' => $specialFieldId, 
					'displayName' => self::$SPECIAL_FIELDS[$specialFieldId]['displayName']
				);
				
				array_unshift($results, $field);
			}
		}
		
		//
		// append the payment fields, if any. 
		//
		foreach(array('total_cost', 'total_paid', 'remaining_balance') as $specialFieldId) {
			$column = self::$SPECIAL_FIELDS[$specialFieldId]['dbColumn'];
			if($report[$column] === 'T') {
				$field = array(
					'id' => $specialFieldId, 
					'displayName' => self::$SPECIAL_FIELDS[$specialFieldId]['displayName']
				);
				
				array_push($results, $field);
			}
		}
		
		// the details column doesn't have a heading title.
		array_push($results, array('id' => 'details', 'displayName' => ''));
		
		return $results;
	}
	
	private function getReportFieldValues($registration) {
		return $this->getReportFieldValuesByRegistrationId($registration['id']);
	}
	
	/**
	 * field values [(field id) -> (value | [values])].
	 */
	private function getReportFieldValuesByRegistrationId($registrationId) { 
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
				Report_ContactField
			ON
				Report_ContactField.contactFieldId = ContactField.id
			WHERE
				Registration_Information.registrationId = :registrationId
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
	
	private function getSpecialFieldValues($report, $result, $processedGroupIds) {
		$fieldValues = array();
		
		foreach(array('registration_type', 'category', 'date_cancelled', 'date_registered') as $specialFieldId) {
			$column = self::$SPECIAL_FIELDS[$specialFieldId]['dbColumn'];
			$valueColumn = self::$SPECIAL_FIELDS[$specialFieldId]['dbValueColumn'];

			if($report[$column] === 'T') {
				$fieldValues[$specialFieldId] = $result[$valueColumn];
			}
		}
			
		// add special payment fields. this info should only be shown once per group. additional
		// group members will have these cells blank.
		$groupId = $result['groupId'];
		if(!in_array($groupId, $processedGroupIds)) {
			if($report[self::$SPECIAL_FIELDS['total_cost']['dbColumn']] === 'T') {
				$cost = db_reg_GroupManager::getInstance()->findTotalCost($groupId);
				$fieldValues['total_cost'] = '$'.number_format($cost, 2);
			}
			if($report[self::$SPECIAL_FIELDS['total_paid']['dbColumn']] === 'T') {
				$paid = db_reg_GroupManager::getInstance()->findTotalPaid($groupId);
				$fieldValues['total_paid'] = '$'.number_format($paid, 2);
			}
			if($report[self::$SPECIAL_FIELDS['remaining_balance']['dbColumn']] === 'T') {
				$cost = db_reg_GroupManager::getInstance()->findTotalCost($groupId);
				$paid = db_reg_GroupManager::getInstance()->findTotalPaid($groupId);
				$remaining = $cost - $paid;
				$fieldValues['remaining_balance'] = '$'.number_format($remaining, 2);
			}
		}
		
		return $fieldValues;
	}
	
	public function createPaymentsToDate($eventId) {
		$sql = '
			INSERT INTO
				Report (
					eventId,
					name,
					isPaymentsToDate	
				)
			VALUES (
				:eventId,
				:name,
				:isPaymentsToDate
			)
		';
		
		$params = array(
			'eventId' => $eventId,
			'name' => 'Payments To Date',
			'isPaymentsToDate' => 'T'
			
		);
		
		$this->execute($sql, $params, 'Create payments to date report.');
		
		return $this->lastInsertId();
	}
	
	public function findPaymentsToDate($eventId) {
		$sql = '
			SELECT
				id,
				eventId,
				name,
				showDateRegistered,
				showDateCancelled,
				showCategory,
				showRegType,
				showTotalCost,
				showTotalPaid,
				showRemainingBalance,
				isPaymentsToDate
			FROM
				Report
			WHERE
				eventId = :eventId
			AND
				isPaymentsToDate = :isPaymentsToDate
		';
		
		$params = array(
			'eventId' => $eventId,
			'isPaymentsToDate' => 'T'
		);
		
		return $this->queryUnique($sql, $params, 'Find payments to date report.');
	}
	
	public function findReportFieldHeadings($reportId) {
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
			'reportId' => $reportId
		);
		
		return $this->rawQuery($sql, $params, 'Find report contact field names.');
	}

	public function findReportRegistrationValues($reportId) {
		$sql = '
			SELECT
				Registration.id as registrationId,
				Registration.regGroupId as groupId,
				Registration.dateRegistered,
				Registration.dateCancelled,
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
			'reportId' => $reportId
		);
		
		return $this->rawQuery($sql, $params, 'Find report special values.');
	}
	
	/**
	 * payment values [(reg group id) -> (cost, paid, balance)].
	 */
	public function findReportPaymentValues($reportId) {
		$sql = '
			SELECT DISTINCT
				Registration.regGroupId
			FROM
				Registration
			INNER JOIN
				Report
			ON
				Registration.eventId = Report.eventId
			WHERE
				Report.id = :reportId
		';
		
		$params = array(
			'reportId' => $reportId
		);
		
		$regGroupIds = $this->rawQuery($sql, $params, 'Find reg groups for event by report.');

		$paymentValues = array();
		foreach($regGroupIds as $regGroupId) {
			$regGroupId = $regGroupId['regGroupId'];
			$cost = db_reg_GroupManager::getInstance()->findTotalCost($regGroupId);
			$paid = db_reg_GroupManager::getInstance()->findTotalPaid($regGroupId);
			
			$paymentValues[$regGroupId] = array(
				'cost' => $cost,
				'paid' => $paid,
				'balance' => $cost - $paid
			);	
		}
		
		return $paymentValues;
	}
	
	/**
	 * report field values [(registration id) -> [(field id) -> (value | [values])]].
	 */
	public function findReportFieldValues($reportId) {
		// find the registrations first.
		$sql = '
			SELECT 
				Registration.id
			FROM
				Registration
			INNER JOIN
				Report
			ON 
				Registration.eventId = Report.eventId
			WHERE
				Report.id = :reportId
		';
		
		$params = array(
			'reportId' => $reportId
		);
		
		$regIds = $this->rawQuery($sql, $params, 'Find registration ids by report.');
		
		// get the values for each registration.
		$values = array();
		foreach($regIds as $regId) { 
			$regId = $regId['id']; 
			$values[$regId] = $this->getReportFieldValuesByRegistrationId($regId);
		}
		
		return $values;
	}
}

?>