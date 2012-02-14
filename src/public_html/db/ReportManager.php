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
	private static $SPECIAL_FIELDS = array(
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
		
		$obj['fields'] = db_ReportFieldManager::getInstance()->findByReport(array(
			'eventId' => $obj['eventId'], 
			'reportId' => $obj['id']
		));
		
		return $obj;
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_ReportManager();
		}
		
		return self::$instance;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
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
				isPaymentsToDate,
				isAllRegToDate,
				isOptionCount,
				isRegTypeBreakdown
			FROM
				Report
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find report.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEvent($params) {
		return $this->findByEventId($params);
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEventId($params) {
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
				isPaymentsToDate,
				isAllRegToDate,
				isOptionCount,
				isRegTypeBreakdown
			FROM
				Report
			WHERE
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->query($sql, $params, 'Find reports by event.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, name]
	 */
	public function createReport($params) {
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
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'name'));
		
		$this->execute($sql, $params, 'Create report.');
		
		return $this->lastInsertId();
	}
	
	/**
	 * 
	 * @param array $params [eventId, reportId]
	 */
	public function deleteReport($params) {
		// delete report fields.
		$sql = '
			DELETE FROM
				Report_ContactField
			WHERE
				Report_ContactField.reportId = :reportId
			AND
				Report_ContactField.reportId
			IN (
				SELECT Report.id 
				FROM Report
				WHERE Report.id = :reportId
				AND Report.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'reportId'));
		
		$this->execute($sql, $params, 'Delete report fields.');
		
		// delete report.
		$sql = '
			DELETE FROM
				Report
			WHERE
				id = :reportId
			AND
				eventId = :eventId
		';
		
		$this->execute($sql, $params, 'Delete report.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, name]
	 */
	public function saveReport($params) {
		$sql = '
			UPDATE
				Report
			SET
				name = :name
			WHERE
				id = :id
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id', 'name'));
		
		$this->execute($sql, $params, 'Save report.');
	}

	/**
	 * 
	 * @param array $params [eventId, reportId, fieldName]
	 */
	public function addSpecialField($params) {
		$column = self::$SPECIAL_FIELDS[$params['fieldName']]['dbColumn'];
		
		$sql = "
			UPDATE
				Report
			SET
				{$column} = :value
			WHERE
				id = :reportId
			AND
				eventId = :eventId
		";
				
		$params = array(
			'reportId' => $params['reportId'],
			'eventId' => $params['eventId'],
			'value' => 'T'
		);
		
		$this->execute($sql, $params, 'Add special field to report.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, reportId, fieldName]
	 */
	public function removeSpecialField($params) {
		$column = self::$SPECIAL_FIELDS[$params['fieldName']]['dbColumn'];
		
		$sql = "
			UPDATE
				Report
			SET
				{$column} = :value
			WHERE
				id = :reportId
			AND
				eventId = :eventId
		";
				
		$params = array(
			'reportId' => $params['reportId'],
			'eventId' => $params['eventId'],
			'value' => 'F'
		);
		
		$this->execute($sql, $params, 'Remove special field from report.');
	}
	
	//////////////////////////////////////////////////////////////
	// methods for running reports.
	//////////////////////////////////////////////////////////////
	
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
	
	/**
	 * field values [(field id) -> (value | [values])].
	 * 
	 * @param array $params ['reportId', 'registrationId']
	 */
	private function getReportFieldValuesByRegistrationId($params) { 
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
				Report_ContactField.reportId = :reportId
			AND
				ContactField.formInputId 
			IN
				(1, 2)
		';
		
		$results = $this->rawQuery($sql, $params, 'Find report field values.');
		
		$values = array();
		foreach($results as $result) {
			$values[$result['id']] = $result['value'];
		}
		
		// multiple input fields (checkbox, radio, select).
		$sql = '
			SELECT 
				ContactField.id,
				ContactFieldOption.displayName AS value
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
			INNER JOIN
				ContactFieldOption
			ON
				Registration_Information.value = ContactFieldOption.id
			WHERE
				Registration_Information.registrationId = :registrationId
			AND
				Report_ContactField.reportId = :reportId
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
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function createRegTypeBreakdown($params) {
		$sql = '
			INSERT INTO
				Report (
					eventId,
					name,
					isRegTypeBreakdown
				)
			VALUES (
				:eventId,
				:name,
				:isRegTypeBreakdown
			)
		';	
		
		$params = array(
			'eventId' => $params['eventId'],
			'name' => 'Registration Type Breakdown',
			'isRegTypeBreakdown' => 'T'
		);
		
		$this->execute($sql, $params, 'Create reg type breakdown report.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function createOptionCount($params) {
		$sql = '
			INSERT INTO
				Report (
					eventId,
					name,
					isOptionCount
				)
			VALUES (
				:eventId,
				:name,
				:isOptionCount
			)
		';	
		
		$params = array(
			'eventId' => $params['eventId'],
			'name' => 'Option Counts',
			'isOptionCount' => 'T'
		); 
		
		$this->execute($sql, $params, 'Create option count report.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function createAllRegToDate($params) {
		$sql = '
			INSERT INTO
				Report (
					eventId,
					name,
					isAllRegToDate
				)
			VALUES (
				:eventId,
				:name,
				:isAllRegToDate
			)
		';	
		
		$params = array(
			'eventId' => $params['eventId'],
			'name' => 'All Registrations To Date',
			'isAllRegToDate' => 'T'
		);
		
		$this->execute($sql, $params, 'Create all reg to date report.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function createPaymentsToDate($params) {
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
			'eventId' => $params['eventId'],
			'name' => 'Payments To Date',
			'isPaymentsToDate' => 'T'
			
		);
		
		$this->execute($sql, $params, 'Create payments to date report.');
	}

	/**
	 * 
	 * @param array $params [reportId]
	 */
	public function findReportFieldHeadings($params) {
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
		
		$params = ArrayUtil::keyIntersect($params, array('reportId'));
		
		return $this->rawQuery($sql, $params, 'Find report contact field names.');
	}

	/**
	 * 
	 * @param array $params [reportId]
	 */
	public function findReportRegistrationValues($params) {
		$sql = '
			SELECT
				Registration.id AS registrationId,
				Registration.regGroupId AS groupId,
				Registration.dateRegistered,
				Registration.dateCancelled,
				Category.displayName AS categoryName,
				RegType.description AS regTypeName
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
			ASC
		';
		
		$params = ArrayUtil::keyIntersect($params, array('reportId'));
		
		return $this->rawQuery($sql, $params, 'Find report special values.');
	}
	
	/**
	 * payment values [(reg group id) -> (cost, paid, balance)].
	 * 
	 * @param array $params [eventId, reportId]
	 */
	public function findReportPaymentValues($params) {
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
			AND
				Report.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'reportId'));
		
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
	 * 
	 * @param array $params [eventId, reportId]
	 */
	public function findReportFieldValues($params) {
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
			AND
				Report.eventId = :eventId
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'reportId'));
		
		$regIds = $this->rawQuery($sql, $params, 'Find registration ids by report.');
		
		// get the values for each registration.
		$values = array();
		foreach($regIds as $regId) { 
			$regId = $regId['id']; 
			$values[$regId] = $this->getReportFieldValuesByRegistrationId(array('reportId' => $params['reportId'], 'registrationId' => $regId));
		}
		
		return $values;
	}
	
	public function findRegIdsMatchingSearch($eventId, $searchTerm, $searchFieldId) {
		// single value fields.
		$sql = "
			SELECT
				Registration_Information.registrationId
			FROM
				Registration_Information
			INNER JOIN
				ContactField
			ON
				ContactField.id = Registration_Information.contactFieldId
			WHERE
				ContactField.formInputId
			IN
				(1, 2)
			AND
				Registration_Information.contactFieldId = :searchFieldId
			AND
				Registration_Information.value 
			LIKE
				CONCAT(:searchTerm, '%')
		";
		
		$params = array(
			'searchTerm' => $searchTerm,
			'searchFieldId' => $searchFieldId
		);
		
		$results = $this->rawQuery($sql, $params, 'Find reg ids matching search.');
		
		// we don't know if the search field is a multi value field, so we run this query
		// if the previous one doesn't return any results. if the search field is not a 
		// multi value field and the previous search is empty, then this one will be 
		// emtpy too, but no harm done just a little wasted effort. we do it this way
		// because it's harder to determine the field type beforehand.
		 
		if(empty($results)) {
			// multiple value fields.
			$sql = "
				SELECT DISTINCT
					Registration_Information.registrationId
				FROM
					Registration_Information
				INNER JOIN
					ContactField
				ON
					Registration_Information.contactFieldId = ContactField.id
				INNER JOIN
					ContactFieldOption
				ON
					Registration_Information.value = ContactFieldOption.id
				WHERE
					Registration_Information.contactFieldId = :searchFieldId
				AND
					ContactField.formInputId 
				IN
					(3, 4, 5)
				AND
					ContactFieldOption.displayName
				LIKE
					CONCAT('%', :searchTerm, '%')	
			";
			
			$results = $this->rawQuery($sql, $params, 'Find reg ids matching search.');
		}
		
		$ids = array();
		foreach($results as $result) {
			$ids[] = $result['registrationId'];
		}
		
		return $ids;
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findAllRegToDateValues($params) {
		$sql = '
			(
				SELECT
					Registration.regGroupId,
					Registration.id AS regId,
					RegType.code AS regTypeCode,
					Registration_RegOption.dateAdded,
					Registration_RegOption.dateCancelled,
					null as lastModified,
					RegOption.code AS optionCode,
					RegOption.description AS optionName,
					RegOptionPrice.description AS priceName,
					RegOptionPrice.price AS price,
					1 AS quantity	
				FROM
					Registration
				INNER JOIN
					Registration_RegOption
				ON
					Registration.id = Registration_RegOption.registrationId
				INNER JOIN
					RegOption
				ON
					RegOption.id = Registration_RegOption.regOptionId
				INNER JOIN
					RegOptionPrice
				ON
					RegOptionPrice.id = Registration_RegOption.priceId
				INNER JOIN
					RegType
				ON
					RegType.id = Registration.regTypeId
				WHERE
					Registration.eventId = :eventId
			)
			UNION ALL
			(
				SELECT
					Registration.regGroupId,
					Registration.id AS regId,
					RegType.code AS regTypeCode,
					null AS dateAdded,
					null AS dateCancelled,
					Registration_VariableQuantityOption.lastModified,
					VariableQuantityOption.code AS optionCode,
					VariableQuantityOption.description AS optionName,
					RegOptionPrice.description AS priceName,
					RegOptionPrice.price AS price,
					Registration_VariableQuantityOption.quantity	
				FROM
					Registration
				INNER JOIN
					Registration_VariableQuantityOption
				ON
					Registration.id = Registration_VariableQuantityOption.registrationId
				INNER JOIN
					VariableQuantityOption
				ON
					VariableQuantityOption.id = Registration_VariableQuantityOption.variableQuantityId
				INNER JOIN
					RegOptionPrice
				ON
					RegOptionPrice.id = Registration_VariableQuantityOption.priceId
				INNER JOIN
					RegType
				ON
					RegType.id = Registration.regTypeId
				WHERE
					Registration.eventId = :eventId
			)
			ORDER BY
				regGroupId, regId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->rawQuery($sql, $params, 'Find all reg to date values.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findOptionCounts($params) {
		$sql = '
			(
				SELECT
					RegOption.code AS optionCode,
					RegOption.description AS optionName,
					RegOptionPrice.description AS priceName,
					RegOptionPrice.price AS price,
					COUNT(priceId) AS priceCount,
					COUNT(priceId)*RegOptionPrice.price AS revenue
				FROM 
					RegOption
				INNER JOIN
					Registration_RegOption
				ON
					RegOption.id = Registration_RegOption.regOptionId
				INNER JOIN 
					RegOptionPrice
				ON
					Registration_RegOption.priceId = RegOptionPrice.id
				INNER JOIN
					Registration
				ON
					Registration.id = Registration_RegOption.registrationId
				WHERE
					Registration.eventId = :eventId
				AND
					Registration_RegOption.dateCancelled is null
				GROUP BY
					Registration_RegOption.priceId
			)
			UNION ALL
			(
				SELECT 
					VariableQuantityOption.code AS optionCode,
					VariableQuantityOption.description AS optionName,
					RegOptionPrice.description AS priceName,
					RegOptionPrice.price AS price,
					sum(Registration_VariableQuantityOption.quantity) AS priceCount,
					sum(Registration_VariableQuantityOption.quantity)*RegOptionPrice.price AS revenue
				FROM
					VariableQuantityOption
				INNER JOIN
					Registration_VariableQuantityOption
				ON
					VariableQuantityOption.id = Registration_VariableQuantityOption.variableQuantityId
				INNER JOIN
					RegOptionPrice
				ON
					Registration_VariableQuantityOption.priceId = RegOptionPrice.id
				INNER JOIN
					Registration
				ON 
					Registration.id = Registration_VariableQuantityOption.registrationId
				WHERE
					Registration.eventId = :eventId
				AND
					Registration_VariableQuantityOption.quantity > 0
				GROUP BY
					Registration_VariableQuantityOption.priceId
			)
			ORDER BY 
				optionCode, price
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->rawQuery($sql, $params, 'Find option counts.');
	}
	
	/**
	 * 
	 * @param array $eventId [eventId]
	 */
	public function findRegTypeBreakdown($params) {
		$sql = '
			SELECT
				Registration.regTypeId AS regTypeId, 
				RegType.description AS regTypeName, 
				count(Registration.regTypeId) AS regTypeCount 
			FROM 
				Registration 
			INNER JOIN
				RegType
			ON
				Registration.regTypeId = RegType.id
			WHERE 
				Registration.eventId = :eventId
			AND
				Registration.dateCancelled is NULL
			GROUP BY 
				Registration.regTypeId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->rawQuery($sql, $params, 'Find reg type breakdown.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$reports = $this->findByEventId($params);
		
		foreach($reports as $r) {
			$this->deleteReport($r);
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findInfoByEventId($params) {
		return $this->select(
			'Report', 
			array(
				'id', 
				'eventId', 
				'name'
			), 
			array(
				'eventId' => $params['eventId']
			)
		);
	}
	
	/**
	 * Delete reports.
	 * @param array $params ['eventId', 'reportIds']
	 */
	public function deleteReports($params) {
		foreach($params['reportIds'] as $reportId) {
			$this->deleteReport(array(
				'eventId' => $params['eventId'],
				'reportId' => $reportId
			));
		}
	}
	
	/**
	 * Find report. 
	 * @param array $params [eventId, id]
	 */
	public function findReport($params) {
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
				isPaymentsToDate,
				isAllRegToDate,
				isOptionCount,
				isRegTypeBreakdown
			FROM
				Report
			WHERE
				eventId = :eventId
			AND
				id = :id
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find report.');
	}
}

?>