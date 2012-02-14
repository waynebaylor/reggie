<?php

class db_ReportFieldManager extends db_OrderableManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Report_ContactField';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_ReportFieldManager();
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
				Report_ContactField.id,
				Report_ContactField.reportId,
				Report_ContactField.contactFieldId,
				Report_ContactField.displayOrder,
				ContactField.displayName
			FROM
				Report_ContactField
			INNER JOIN
				ContactField
			ON
				Report_ContactField.contactFieldId = ContactField.id
			WHERE
				Report_ContactField.id = :id
			AND
				ContactField.eventId = :eventId
			ORDER BY
				Report_ContactField.displayOrder
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find report field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, reportId]
	 */
	public function findByReport($params) {
		$sql = '
			SELECT
				Report_ContactField.id,
				Report_ContactField.reportId,
				Report_ContactField.contactFieldId,
				Report_ContactField.displayOrder,
				ContactField.displayName
			FROM
				Report_ContactField
			INNER JOIN
				ContactField
			ON
				Report_ContactField.contactFieldId = ContactField.id
			WHERE
				Report_ContactField.reportId = :reportId
			AND
				ContactField.eventId = :eventId
			ORDER BY
				Report_ContactField.displayOrder
		';
	
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'reportId'));
		
		return $this->query($sql, $params, 'Find report fields.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, reportId, contactFieldId]
	 */
	public function createField($params) {
		$this->checkReportPermission($params);
		
		$sql = '
			INSERT INTO
				Report_ContactField (
					reportId,
					contactFieldId,
					displayOrder
				)
			VALUES (
				:reportId,
				:contactFieldId,
				:displayOrder
			)
		';
		
		$params = array(
			'reportId' => $params['reportId'],
			'contactFieldId' => $params['contactFieldId'],
			'displayOrder' => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Create report field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function deleteField($params) {
		$sql = '
			DELETE FROM
				Report_ContactField
			WHERE
				Report_ContactField.id = :id
			AND
				Report_ContactField.reportId 
			IN (
				SELECT Report.id
				FROM Report
				WHERE Report.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete report field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, reportId, id]
	 */
	public function moveFieldUp($params) {
		$this->checkReportPermission($params);
		
		$this->moveUp($params, 'reportId', $params['reportId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, reportId, id]
	 */
	public function moveFieldDown($params) {
		$this->checkReportPermission($params);
		
		$this->moveDown($params, 'reportId', $params['reportId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, reportId]
	 */
	private function checkReportPermission($params) {
		$results = $this->rawSelect(
			'Report', 
			array(
				'id', 
				'eventId'
			), 
			array(
				'id' => $params['reportId'],
				'eventId' => $params['eventId']
			)
		);
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to modify Report_ContactField. (event id, report id) -> ({$params['eventId']}, {$params['reportId']}).");
		}
	}
}

?>